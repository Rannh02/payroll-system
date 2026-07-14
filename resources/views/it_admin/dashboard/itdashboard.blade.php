@extends('it_admin.layouts.master')

@section('title', 'Dashboard - VIA Architects Associates')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/itdashboard/itdashboard.css') }}">
@endsection

@section('content')
<div class="max-w-[1600px] mx-auto it-admin-dashboard">

    {{-- Page Header --}}
    <div class="content-header">
        <div>
            <h2 class="header-title">Dashboard Overview</h2>
            <p class="header-subtitle">
                <span class="subtitle-dot"></span>
                Welcome back, IT Administrator!
            </p>
        </div>
        <div class="header-actions">
            <button class="btn-secondary">
                <i data-lucide="calendar" class="h-4 w-4 text-blue-500"></i>
                Last 30 Days
            </button>
            <button class="btn-primary">
                <i data-lucide="plus" class="h-4 w-4"></i>
                New Report
            </button>
        </div>
    </div>

    {{-- Top stat cards (row 1) --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon stat-icon-teal">
                    <i data-lucide="users" class="h-6 w-6"></i>
                </div>
            </div>
            <h3 class="stat-label">Total Users</h3>
            <p class="stat-value">{{ $totalUsers }}</p>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon stat-icon-indigo">
                    <i data-lucide="log-in" class="h-6 w-6"></i>
                </div>
            </div>
            <h3 class="stat-label">Active Users</h3>
            <p class="stat-value">{{ $activeUsers }}</p>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon stat-icon-amber">
                    <i data-lucide="alert-triangle" class="h-6 w-6"></i>
                </div>
            </div>
            <h3 class="stat-label">Failed Logins</h3>
            <p class="stat-value">{{ $failedLogins }}</p>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon stat-icon-red">
                    <i data-lucide="lock" class="h-6 w-6"></i>
                </div>
            </div>
            <h3 class="stat-label">Locked Accounts</h3>
            <p class="stat-value">{{ $lockedAccounts }}</p>
        </div>
    </div>

    {{-- Top stat cards (row 2) --}}
    <div class="stats-grid stats-grid-3">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon stat-icon-blue">
                    <i data-lucide="shield" class="h-6 w-6"></i>
                </div>
            </div>
            <h3 class="stat-label">Security Alerts</h3>
            <p class="stat-value">{{ $alerts }}</p>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon stat-icon-rose">
                    <i data-lucide="user-x" class="h-6 w-6"></i>
                </div>
            </div>
            <h3 class="stat-label">Audit Logs</h3>
            <p class="stat-value">{{ $auditLogs }}</p>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon stat-icon-rose">
                    <i data-lucide="user-check" class="h-6 w-6"></i>
                </div>
            </div>
            <h3 class="stat-label">Backups</h3>
            <p class="stat-value">{{ $backupCount }}</p>
        </div>
    </div>

    {{-- ════ LOWER TABLE SECTIONS ════ --}}
    <div class="it-lower-stack">

        {{-- ── RECENT ALERTS ─────────────────────────── --}}
        <div class="it-panel">
            <div class="it-panel-header">
                <div class="it-panel-title-group">
                    <div class="it-panel-icon it-panel-icon-red">
                        <i data-lucide="alert-circle" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <h3 class="it-panel-heading">Recent Alerts</h3>
                        <p class="it-panel-subtitle">Latest system alerts and lockouts</p>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <span class="it-panel-badge">
                        <i data-lucide="clock" class="h-3 w-3"></i> Live
                    </span>
                    <a href="{{ route('it_admin.security_logs') }}" class="it-view-all">
                        View All <i data-lucide="arrow-right" class="h-3 w-3"></i>
                    </a>
                </div>
            </div>

            <table class="it-table">
                <thead>
                    <tr>
                        <th style="width:220px;">Date &amp; Time</th>
                        <th>User</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAlerts as $alert)
                        @php
                            $s = strtolower($alert->status ?? '');
                            $badgeClass = str_contains($s, 'lock') ? 'it-status-locked'
                                        : (str_contains($s, 'warn') ? 'it-status-warning' : 'it-status-failed');
                            $badgeIcon  = str_contains($s, 'lock') ? 'lock'
                                        : (str_contains($s, 'warn') ? 'alert-triangle' : 'x-circle');
                            $displayName = $alert->user?->name ?? $alert->email;
                            $initials    = strtoupper(substr($displayName, 0, 2));
                        @endphp
                        <tr>
                            <td>
                                <div class="it-date-cell">
                                    <span class="it-date-main">{{ $alert->created_at->format('M d, Y') }}</span>
                                    <span class="it-date-time">{{ $alert->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="it-user-cell">
                                    <div class="it-avatar it-avatar-red">{{ $initials }}</div>
                                    <div>
                                        <div class="it-user-name">{{ $displayName }}</div>
                                        @if($alert->user?->name)
                                            <div class="it-user-email">{{ $alert->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="it-status-badge {{ $badgeClass }}">
                                    {{ strtoupper($alert->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <div class="it-empty-state">
                                    <i data-lucide="shield-check" class="h-10 w-10" style="opacity:.2; display:block; margin:0 auto 0.75rem;"></i>
                                    No alerts found. System is clean.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── RECENT LOGINS ─────────────────────────── --}}
        <div class="it-panel">
            <div class="it-panel-header">
                <div class="it-panel-title-group">
                    <div class="it-panel-icon it-panel-icon-green">
                        <i data-lucide="check-circle" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <h3 class="it-panel-heading">Recent Logins</h3>
                        <p class="it-panel-subtitle">Most recent successful access events</p>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <span class="it-panel-badge" style="background:#ecfdf5; color:#059669; border-color:#a7f3d0;">
                        <i data-lucide="check" class="h-3 w-3"></i> Successful
                    </span>
                    <a href="{{ route('it_admin.security_logs') }}" class="it-view-all">
                        View All <i data-lucide="arrow-right" class="h-3 w-3"></i>
                    </a>
                </div>
            </div>

            <table class="it-table">
                <thead>
                    <tr>
                        <th style="width:220px;">Date &amp; Time</th>
                        <th>User</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLogins as $login)
                        @php
                            $displayName = $login->user?->name ?? $login->email;
                            $initials    = strtoupper(substr($displayName, 0, 2));
                        @endphp
                        <tr>
                            <td>
                                <div class="it-date-cell">
                                    <span class="it-date-main">{{ $login->created_at->format('M d, Y') }}</span>
                                    <span class="it-date-time">{{ $login->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="it-user-cell">
                                    <div class="it-avatar it-avatar-green">{{ $initials }}</div>
                                    <div>
                                        <div class="it-user-name">{{ $displayName }}</div>
                                        @if($login->user?->name)
                                            <div class="it-user-email">{{ $login->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="it-ip-pill">
                                    <i data-lucide="globe" class="h-3 w-3"></i>
                                    {{ $login->ip_address }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <div class="it-empty-state">
                                    <i data-lucide="log-in" class="h-10 w-10" style="opacity:.2; display:block; margin:0 auto 0.75rem;"></i>
                                    No recent logins found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── USER STATISTICS ───────────────────────── --}}
        @php
            $totalForBar = max($userStats['total_users'], 1);
            $statRows = [
                ['label' => 'Total Users', 'dot' => 'dot-total',     'bar' => 'bar-total',     'count' => $userStats['total_users']],
                ['label' => 'Admins',       'dot' => 'dot-admin',     'bar' => 'bar-admin',     'count' => $userStats['admins']],
                ['label' => 'IT Admins',    'dot' => 'dot-it',        'bar' => 'bar-it',        'count' => $userStats['it_admins']],
                ['label' => 'Superadmins',  'dot' => 'dot-super',     'bar' => 'bar-super',     'count' => $userStats['superadmins']],
                ['label' => 'Employees',    'dot' => 'dot-employee',  'bar' => 'bar-employee',  'count' => $userStats['employees']],
                ['label' => 'Suspended',    'dot' => 'dot-suspended', 'bar' => 'bar-suspended', 'count' => $userStats['suspended']],
            ];
        @endphp

        <div class="it-panel">
            <div class="it-panel-header">
                <div class="it-panel-title-group">
                    <div class="it-panel-icon it-panel-icon-purple">
                        <i data-lucide="bar-chart-3" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <h3 class="it-panel-heading">User Statistics</h3>
                        <p class="it-panel-subtitle">Current user role distribution</p>
                    </div>
                </div>
                <a href="{{ route('it_admin.users') }}" class="it-view-all">
                    Manage Users <i data-lucide="arrow-right" class="h-3 w-3"></i>
                </a>
            </div>

            @foreach($statRows as $row)
                @php $pct = round(($row['count'] / $totalForBar) * 100); @endphp
                <div class="stats-item-row">
                    <span class="stats-item-dot {{ $row['dot'] }}"></span>
                    <span class="stats-item-label">{{ $row['label'] }}</span>
                    <div class="stats-item-bar-wrap">
                        <div class="stats-item-bar {{ $row['bar'] }}" style="width:{{ $pct }}%;"></div>
                    </div>
                    <span class="stats-item-count">{{ $row['count'] }}</span>
                </div>
            @endforeach
        </div>

    </div>{{-- end it-lower-stack --}}
</div>
@endsection