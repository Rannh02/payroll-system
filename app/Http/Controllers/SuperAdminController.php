<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        return view('Superadmin.Admin.Administrator');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['superadmin_id', 'superadmin_username']);
        $request->session()->regenerate();

        return redirect()->route('login');
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

        return view('Superadmin.security.login_logs', compact('logs'));
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
