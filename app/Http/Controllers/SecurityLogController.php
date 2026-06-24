<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use Illuminate\Http\Request;

class SecurityLogController extends Controller
{
    public function loginLogs(Request $request)
    {
        $query = LoginLog::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('browser', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->latest()->paginate(10)->withQueryString();

        return view('admin.security.login_logs', compact('logs'));
    }

    public function unlock(Request $request)
    {
        $request->validate(['log_id' => 'required|exists:login_logs,id']);

        $log = LoginLog::findOrFail($request->log_id);

        // The throttle key used in the login route
        $throttleKey = \Illuminate\Support\Str::transliterate(\Illuminate\Support\Str::lower($log->email) . '|' . $log->ip_address);
        \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);

        // Update the specific log
        $log->update([
            'status' => 'UNLOCKED',
            'locked_until' => null
        ]);

        // Also unlock all other related LOCKED logs for this IP/email combination
        LoginLog::where('email', $log->email)
            ->where('ip_address', $log->ip_address)
            ->where('status', 'LOCKED')
            ->update([
                'status' => 'UNLOCKED',
                'locked_until' => null
            ]);

        return back()->with('success', 'Account unlocked successfully.');
    }

    public function toggleSuspend(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $user = \App\Models\User::findOrFail($request->user_id);
        $user->is_suspended = !$user->is_suspended;
        $user->save();

        $action = $user->is_suspended ? 'suspended' : 'unsuspended';
        return back()->with('success', "User account has been {$action} successfully.");
    }
}
