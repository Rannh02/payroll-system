<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use Illuminate\Http\Request;

class SecurityLogController extends Controller
{
    private function buildLoginQuery(Request $request)
    {
        $query = LoginLog::query();

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

        return $query->latest()->paginate(10)->withQueryString();
    }

    public function loginLogs(Request $request)
    {
        $logs = $this->buildLoginQuery($request);

        return view('admin.admin_security.login_logs', compact('logs'));
    }

    public function loginLogsIT(Request $request)
    {
        $logs = $this->buildLoginQuery($request);

        return view('it_admin.securitylogs.securitylogs', compact('logs'));
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
        return back()->with('info', 'User suspension is disabled because the legacy users table has been removed.');
    }
}
