@extends('layouts.master')

@section('title', 'Dashboard - VIA Architects Associates')

@section('content')
    <div class="max-w-[1600px] mx-auto it-admin-dashboard">
        <div class="content-header">
            <div>
                <h2 class="header-title">Dashboard Overview</h2>
                <p class="header-subtitle">
                    <span class="subtitle-dot"></span>
                    Welcome back,IT Administrator!
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

        <div id="itAdminLowerStack" class="dashboard-lower-stack" style="display: flex; flex-direction: column; gap: 1.5rem; margin-top: 2rem; width: 100%;">
            <section class="dashboard-panel" style="width: 100% !important;">
                <div class="dashboard-panel-header">
                    <div class="dashboard-panel-title">
                        <div class="panel-icon panel-icon-red">
                            <i data-lucide="alert-circle" class="h-6 w-6"></i>
                        </div>
                        <div>
                            <h3 class="dashboard-panel-heading">Recent Alerts</h3>
                            <p class="dashboard-panel-subtitle">Latest system alerts and lockouts</p>
                        </div>
                    </div>
                </div>
                <div class="dashboard-panel-body">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAlerts as $alert)
                                <tr>
                                    <td>{{ $alert->created_at->format('M d, Y h:i A') }}</td>
                                    <td>{{ $alert->user?->name ?? $alert->email }}</td>
                                    <td>{{ $alert->status }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="empty-state">No alerts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="dashboard-panel">
                <div class="dashboard-panel-header">
                    <div class="dashboard-panel-title">
                        <div class="panel-icon panel-icon-green">
                            <i data-lucide="check-circle" class="h-6 w-6"></i>
                        </div>
                        <div>
                            <h3 class="dashboard-panel-heading">Recent Logins</h3>
                            <p class="dashboard-panel-subtitle">Most recent successful access events</p>
                        </div>
                    </div>
                </div>
                <div class="dashboard-panel-body">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLogins as $login)
                                <tr>
                                    <td>{{ $login->created_at->format('M d, Y h:i A') }}</td>
                                    <td>{{ $login->user?->name ?? $login->email }}</td>
                                    <td>{{ $login->ip_address }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="empty-state">No recent logins.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="dashboard-panel">
                <div class="dashboard-panel-header">
                    <div class="dashboard-panel-title">
                        <div class="panel-icon panel-icon-purple">
                            <i data-lucide="bar-chart-3" class="h-6 w-6"></i>
                        </div>
                        <div>
                            <h3 class="dashboard-panel-heading">User Statistics</h3>
                            <p class="dashboard-panel-subtitle">Current user role distribution</p>
                        </div>
                    </div>
                </div>
                <div class="dashboard-panel-body dashboard-panel-list">
                    <table class="dashboard-table">
                        <tbody>
                            <tr>
                                <td><strong>Total Users</strong></td>
                                <td>{{ $userStats['total_users'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Admins</strong></td>
                                <td>{{ $userStats['admins'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>IT Admins</strong></td>
                                <td>{{ $userStats['it_admins'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Superadmins</strong></td>
                                <td>{{ $userStats['superadmins'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Employees</strong></td>
                                <td>{{ $userStats['employees'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Suspended</strong></td>
                                <td>{{ $userStats['suspended'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
@endsection