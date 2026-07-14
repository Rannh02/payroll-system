<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Log an action to the audit logs.
     *
     * @param string $action
     * @param string|null $description
     * @return void
     */
    public static function log(string $action, ?string $description = null): void
    {
        $userName = 'System / Guest';
        $role = 'unknown';

        // Check Admin guard
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $userName = $user->name ?? $user->email;
            $role = $user->role ?? 'admin';
        }
        // Check Employee guard
        elseif (Auth::guard('employee')->check()) {
            $user = Auth::guard('employee')->user();
            $userName = $user->name ?? $user->email;
            $role = 'Employee';
        }
        // Check Superadmin via session (middleware logic)
        elseif (session()->has('superadmin_id')) {
            $userName = session('superadmin_username') ?? 'Superadmin';
            $role = 'Superadmin';
        }

        $userAgent = Request::header('User-Agent');
        $browser = class_exists(\App\Models\LoginLog::class) 
            ? \App\Models\LoginLog::parseBrowser($userAgent) 
            : 'Unknown';

        AuditLog::create([
            'user_name' => $userName,
            'role' => $role,
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
            'browser' => $browser,
        ]);
    }
}
