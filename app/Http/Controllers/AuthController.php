<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Models\EmployeeAuth;
use App\Models\LoginLog;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        Log::info('Login attempt for: ' . $request->email);

        $rules = [
            'email'    => ['required'],
            'password' => ['required'],
            // Always require the checkbox to be clicked
            'g-recaptcha-response' => ['required'],
        ];

        // On non-local environments, also verify the token with Google's API
        if (!app()->environment('local', 'testing')) {
            $rules['g-recaptcha-response'][] = function ($attribute, $value, $fail) {
                $secret = config('services.recaptcha.secret_key');
                if (empty($secret)) {
                    return;
                }

                try {
                    $response = Http::asForm()
                        ->timeout(5)
                        ->post('https://www.google.com/recaptcha/api/siteverify', [
                            'secret'   => $secret,
                            'response' => $value,
                            'remoteip' => request()->ip(),
                        ]);

                    $data = $response->json();
                    Log::debug('reCAPTCHA verify response', $data ?? []);

                    if (!($data['success'] ?? false)) {
                        $fail('The reCAPTCHA validation failed. Please try again.');
                    }
                } catch (\Exception $e) {
                    Log::warning('reCAPTCHA verification error: ' . $e->getMessage());
                    // Fail open: don't block login if Google API is unreachable
                    return;
                }
            };
        }

        $request->validate($rules, [
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA checkbox.',
        ]);

        $credentials = $request->only('email', 'password');

        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
        $maxAttempts = 3;
        $decayMinutes = 15;

        $browser = LoginLog::parseBrowser($request->userAgent());

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $lockedUntil = now()->addSeconds($seconds);
            
            LoginLog::create([
                'user_id' => null,
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'browser' => $browser,
                'status' => 'LOCKED',
                'locked_until' => $lockedUntil,
            ]);

            return back()->withErrors([
                'email' => "Too many login attempts. Your account is on hold.",
            ])->onlyInput('email');
        }

        if (false) {
            LoginLog::create([
                'user_id' => null,
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'browser' => $browser,
                'status' => 'SUSPENDED',
            ]);

            return back()->withErrors([
                'email' => 'Your account has been suspended. Please contact the administrator.',
            ])->onlyInput('email');
        }

        // Check if it's a superadmin
        $superadmin = \App\Models\Superadmin::where('username', $request->email)->first();
        if ($superadmin && Hash::check($request->password, $superadmin->password)) {
            RateLimiter::clear($throttleKey);
            Log::info('Superadmin login successful for: ' . $request->email);
            
            $request->session()->put('superadmin_id', $superadmin->superadmin_id);
            $request->session()->put('superadmin_username', $superadmin->username);
            $request->session()->regenerate();

            return redirect()->route('superadmin.dashboard');
        }

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            Log::info('Admin login successful for: ' . $request->email);
            $request->session()->regenerate();

            $adminUser = Auth::guard('admin')->user();
            $request->session()->put('admin_id', $adminUser->id);
            $request->session()->put('admin_email', $adminUser->email);

            LoginLog::create([
                'user_id' => null,
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'browser' => $browser,
                'status' => 'SUCCESS',
            ]);

            if ($adminUser->role === 'it_admin') {
                return redirect()->intended(route('it_admin.dashboard'));
            }

            if ($adminUser->role === 'finance_admin') {
                return redirect()->intended(route('finance_admin.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        if (Auth::guard('employee')->attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            Log::info('Employee login successful for: ' . $request->email);
            $request->session()->regenerate();

            LoginLog::create([
                'user_id' => null,
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'browser' => $browser,
                'status' => 'SUCCESS',
            ]);

            return redirect()->intended(route('user.dashboard'));
        }

        RateLimiter::hit($throttleKey, $decayMinutes * 60);
        Log::warning('Login failed for: ' . $request->email);

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $lockedUntil = now()->addSeconds($seconds);
            $status = 'LOCKED';
        } else {
            $lockedUntil = null;
            $status = 'FAILED';
        }

        LoginLog::create([
            'user_id' => null,
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'browser' => $browser,
            'status' => $status,
            'locked_until' => $lockedUntil,
        ]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showPasswordRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = EmployeeAuth::where('email', $request->email)->first() ?? Admin::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We could not find an account with that email address.']);
        }

        $broker = $user instanceof Admin ? 'admins' : 'employees';
        $token = Password::broker($broker)->createToken($user);

        try {
            $user->sendPasswordResetNotification($token);
        } catch (\Exception $e) {
            Log::error("Failed to send password reset email: " . $e->getMessage());
        }

        return back()->with([
            'status' => 'A password reset link has been sent to your email address.',
            'resetEmail' => $user->email,
            'resetUrl' => route('password.reset', ['token' => $token, 'email' => $user->email]),
        ]);
    }

    public function showPasswordResetForm(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $isAdmin = Admin::where('email', $request->email)->exists();
        $broker = $isAdmin ? 'admins' : 'employees';

        $status = Password::broker($broker)->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));
                $user->save();

                // Synchronize password to users table
                $u = \App\Models\User::where('email', $user->email)->first();
                if ($u) {
                    $u->password = Hash::make($password);
                    $u->save();
                }
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Your password has been reset! You may now log in.');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
