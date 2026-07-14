@extends('it_admin.layouts.master')

@section('title', 'Session Management')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/security_logs.css') }}">
    <style>
        .session-notice {
            background: rgba(99,102,241,0.1);
            border: 1px solid rgba(99,102,241,0.3);
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            color: #a5b4fc;
            font-size: 0.875rem;
        }
        .session-notice i { flex-shrink: 0; margin-top: 2px; }

        .session-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .btn-revoke-all {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1rem;
            background: rgba(239,68,68,0.15);
            color: #f87171;
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
        }
        .btn-revoke-all:hover { background: rgba(239,68,68,0.25); }

        .btn-revoke {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.3rem 0.7rem;
            background: rgba(239,68,68,0.12);
            color: #f87171;
            border: 1px solid rgba(239,68,68,0.25);
            border-radius: 0.4rem;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-revoke:hover { background: rgba(239,68,68,0.25); }

        .current-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.55rem;
            background: rgba(34,197,94,0.12);
            color: #4ade80;
            border: 1px solid rgba(34,197,94,0.25);
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .empty-state-box {
            text-align: center;
            padding: 3rem 1.5rem;
            color: var(--text-muted, #9ca3af);
        }
        .empty-state-box i { margin-bottom: 0.75rem; opacity: 0.5; }
        .empty-state-box h3 { font-size: 1rem; color: var(--text-primary, #f1f5f9); margin-bottom: 0.4rem; }
        .empty-state-box p { font-size: 0.85rem; }
    </style>
@endsection

@section('content')
<div class="govt-container">
    <div class="content-header">
        <div>
            <h2 class="header-title">Session Management</h2>
            <p class="header-subtitle">View and manage all active user sessions in the system</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success-log">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-success-log" style="background: rgba(239,68,68,0.15); color: #f87171; border-color: rgba(239,68,68,0.3);">
            {{ session('error') }}
        </div>
    @endif
    @if(session('info'))
        <div class="alert-success-log" style="background: rgba(99,102,241,0.12); color: #a5b4fc; border-color: rgba(99,102,241,0.3);">
            {{ session('info') }}
        </div>
    @endif

    @if(!$sessionTableExists)
        <div class="session-notice">
            <i data-lucide="info" style="width:18px;height:18px;"></i>
            <div>
                <strong>Database sessions not enabled.</strong> To use Session Management, set
                <code>SESSION_DRIVER=database</code> in your <code>.env</code> file and run
                <code>php artisan session:table && php artisan migrate</code>.
            </div>
        </div>

        <div class="department-table-container">
            <div class="empty-state-box">
                <i data-lucide="monitor-off" style="width:48px;height:48px; display:block; margin: 0 auto 0.75rem;"></i>
                <h3>No Session Data Available</h3>
                <p>Enable the database session driver to track and manage active sessions.</p>
            </div>
        </div>
    @else
        {{-- Filter + Revoke All toolbar --}}
        <div class="session-toolbar">
            <form action="{{ route('it_admin.session_management') }}" method="GET"
                  style="display:flex; gap:0.5rem; flex-wrap:wrap; align-items:center;">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search IP or browser..." class="filter-input" style="max-width:280px;">
                <button type="submit" class="btn-filter-submit">Filter</button>
                <a href="{{ route('it_admin.session_management') }}" class="btn-filter-refresh">Reset</a>
            </form>

            <form action="{{ route('it_admin.session_management.revoke_all') }}" method="POST"
                  onsubmit="return confirm('Revoke all sessions except your current one?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-revoke-all">
                    <i data-lucide="log-out" style="width:14px;height:14px;"></i>
                    Revoke All Other Sessions
                </button>
            </form>
        </div>

        <div class="department-table-container">
            <table class="department-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>IP Address</th>
                        <th>Browser / Device</th>
                        <th>Last Active</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $index => $session)
                        @php
                            $isCurrent = $session->id === $currentSessionId;
                            $lastActive = \Carbon\Carbon::createFromTimestamp($session->last_activity);

                            // Try to parse user info from payload
                            $userName = '—';
                            try {
                                $payload = unserialize(base64_decode($session->payload));
                                $userId = $payload['_token'] ?? null; // fallback
                                // Look up user_id stored in session
                                if (isset($payload['login_web_' . sha1('Illuminate\Auth\SessionGuard')])) {
                                    $userId = $payload['login_web_' . sha1('Illuminate\Auth\SessionGuard')];
                                } elseif (isset($payload['login_admin_' . sha1('Illuminate\Auth\SessionGuard')])) {
                                    $userId = $payload['login_admin_' . sha1('Illuminate\Auth\SessionGuard')];
                                }
                                if ($userId) {
                                    $user = \App\Models\Admin::find($userId) ?? \App\Models\User::find($userId);
                                    $userName = $user ? $user->name : 'User #' . $userId;
                                }
                            } catch (\Exception $e) {
                                $userName = 'Unknown';
                            }

                            // Parse browser from user_agent
                            $agent = $session->user_agent ?? '';
                            $browser = 'Unknown';
                            if (str_contains($agent, 'Edg/') || str_contains($agent, 'Edge/')) $browser = 'Edge';
                            elseif (str_contains($agent, 'Firefox/')) $browser = 'Firefox';
                            elseif (str_contains($agent, 'OPR/')) $browser = 'Opera';
                            elseif (str_contains($agent, 'Chrome/')) $browser = 'Chrome';
                            elseif (str_contains($agent, 'Safari/')) $browser = 'Safari';
                        @endphp
                        <tr>
                            <td>{{ $sessions->firstItem() + $index }}</td>
                            <td>{{ $userName }}</td>
                            <td>{{ $session->ip_address ?? '—' }}</td>
                            <td>{{ $browser }}</td>
                            <td>{{ $lastActive->format('M d, Y h:i A') }}</td>
                            <td>
                                @if($isCurrent)
                                    <span class="current-badge">
                                        <i data-lucide="check" style="width:10px;height:10px;"></i>
                                        Current
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size:0.8rem;">Active</span>
                                @endif
                            </td>
                            <td>
                                @if(!$isCurrent)
                                    <form action="{{ route('it_admin.session_management.revoke', $session->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Revoke this session?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-revoke">
                                            <i data-lucide="x" style="width:11px;height:11px;"></i>
                                            Revoke
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted" style="font-size:0.75rem;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">No active sessions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $sessions->links('vendor.pagination.numbers') }}
        </div>
    @endif
</div>
@endsection
