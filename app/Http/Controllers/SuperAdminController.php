<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        return view('Superadmin.dashboard.index');
    }

    public function analytics()
    {
        return view('Superadmin.Analytics.Analytics');
    }

    public function Administrator()
    {
        $admins = Admin::latest()->get();

        return view('Superadmin.Admin.Administrator', compact('admins'));
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:50'],
            'middle_name' => ['nullable', 'string', 'max:50'],
            'last_name' => ['nullable', 'string', 'max:50'],
            'role' => ['required', Rule::in(['admin', 'it_admin', 'finance_admin'])],
            'email' => ['required', 'email', 'max:255', 'unique:admin,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:16',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],
        ], [
            'password.regex' => 'Password must include uppercase, lowercase, a number, and a special character.',
            'password.min' => 'Password must be between 8 and 16 characters long.',
            'password.max' => 'Password must be between 8 and 16 characters long.',
        ]);

        $fullName = trim($request->input('name') ?: implode(' ', array_filter([
            $request->input('first_name'),
            $request->input('middle_name'),
            $request->input('last_name'),
        ], fn ($value) => filled($value))));

        if (blank($fullName)) {
            throw ValidationException::withMessages([
                'name' => 'Please provide a full name.',
            ]);
        }

        Admin::create([
            'name' => $fullName,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
        ]);

        \App\Services\AuditLogger::log('Created Admin', "Created {$request->role} account for {$fullName}.");

        return back()->with('success', 'Admin account created successfully.');
    }

    public function AuditLogs(Request $request)
    {
        $query = \App\Models\AuditLog::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->latest()->paginate(10)->withQueryString();

        return view('Superadmin.Audit-Logs.AuditLogs', compact('logs'));
    }
    public function logout(Request $request)
    {
        \App\Services\AuditLogger::log('Logged Out', 'Superadmin logged out of the system.');
        $request->session()->forget(['superadmin_id', 'superadmin_username']);
        $request->session()->regenerate();

        return redirect()->route('login');
    }

    public function security(Request $request)
    {
        return $this->securityLogs($request);
    }

    public function securityLogs(Request $request)
    {
        $query = \App\Models\LoginLog::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('browser', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->latest()->paginate(10)->withQueryString();

        return view('Superadmin.security.security-logs', compact('logs'));
    }

    public function toggleSuspend(Request $request)
    {
        return back()->with('info', 'User suspension is disabled because the legacy users table has been removed.');
    }
}
