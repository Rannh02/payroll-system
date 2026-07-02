<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            'name' => ['required', 'string', 'max:255'],
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

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'admin',
        ]);

        return back()->with('success', 'Admin account created successfully.');
    }

    public function AuditLogs()
    {
        return view('Superadmin.Audit-Logs.AuditLogs');
    }
    public function logout(Request $request)
    {
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
        $query = \App\Models\LoginLog::with('user');

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

        return view('Superadmin.security.security-logs', compact('logs'));
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
