<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return view('it_admin.security.securitylogs.securitylogs', compact('logs'));
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

    public function unlockIT(Request $request)
    {
        return $this->unlock($request);
    }

    public function toggleSuspend(Request $request)
    {
        return back()->with('info', 'User suspension is disabled because the legacy users table has been removed.');
    }

    public function toggleSuspendIT(Request $request)
    {
        return back()->with('info', 'User suspension is disabled because the legacy users table has been removed.');
    }

    // ── Audit Logs ────────────────────────────────────────────────────────────
    public function auditLogs(Request $request)
    {
        // Audit logs are built from login_logs enriched with action context.
        // We show all significant events (login, lock, unlock) as an audit trail.
        $query = LoginLog::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('browser', 'like', "%{$search}%");
            });
        }

        if ($request->filled('action')) {
            $query->where('status', $request->action);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $auditLogs = $query->latest()->paginate(15)->withQueryString();

        // Summary stats
        $totalEvents  = LoginLog::count();
        $successCount = LoginLog::where('status', 'SUCCESS')->count();
        $failedCount  = LoginLog::where('status', 'FAILED')->count();
        $lockedCount  = LoginLog::where('status', 'LOCKED')->count();

        return view('it_admin.security.auditslog.auditlogs', compact(
            'auditLogs', 'totalEvents', 'successCount', 'failedCount', 'lockedCount'
        ));
    }

    // ── Session Management ────────────────────────────────────────────────────
    public function sessionManagement(Request $request)
    {
        // Read active sessions from the database sessions table (requires database session driver).
        // Falls back gracefully if the sessions table doesn't exist yet.
        $sessions = collect();
        $sessionTableExists = false;

        try {
            if (DB::getSchemaBuilder()->hasTable('sessions')) {
                $sessionTableExists = true;
                $query = DB::table('sessions')
                    ->orderByDesc('last_activity');

                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->where('ip_address', 'like', "%{$search}%")
                          ->orWhere('user_agent', 'like', "%{$search}%");
                    });
                }

                $sessions = $query->paginate(15)->withQueryString();
            }
        } catch (\Exception $e) {
            // Sessions table not available; show empty state.
        }

        $currentSessionId = session()->getId();

        return view('it_admin.security.session management.session', compact(
            'sessions', 'sessionTableExists', 'currentSessionId'
        ));
    }

    public function revokeSession(Request $request, $sessionId)
    {
        try {
            if (DB::getSchemaBuilder()->hasTable('sessions')) {
                DB::table('sessions')->where('id', $sessionId)->delete();
                return back()->with('success', 'Session revoked successfully.');
            }
        } catch (\Exception $e) {
            // ignore
        }
        return back()->with('error', 'Unable to revoke session.');
    }

    public function revokeAllSessions(Request $request)
    {
        try {
            if (DB::getSchemaBuilder()->hasTable('sessions')) {
                // Keep the current session alive
                DB::table('sessions')
                    ->where('id', '!=', session()->getId())
                    ->delete();
                return back()->with('success', 'All other sessions have been revoked.');
            }
        } catch (\Exception $e) {
            // ignore
        }
        return back()->with('error', 'Unable to revoke sessions.');
    }
}
