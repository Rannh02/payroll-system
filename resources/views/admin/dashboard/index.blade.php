@extends('layouts.master')

@section('title', 'Dashboard - VIA Architects Associates')

@section('content')
    <div class="max-w-[1600px] mx-auto">
        <div class="content-header">
            <div>
                <h2 class="header-title">Dashboard Overview</h2>
                <p class="header-subtitle">
                    <span class="subtitle-dot"></span>
                    Welcome back, Administrator! Here's your current payroll summary.
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

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="users" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Total Employees</h3>
                <p class="stat-value">{{ $totalEmployees }}</p>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-indigo">
                        <i data-lucide="banknote" class="h-6 w-6"></i>
                    </div>
                    <span class="stat-badge badge-teal">Total</span>
                </div>
                <h3 class="stat-label">Payroll Processed</h3>
                <p class="stat-value">{{ $payrollsProcessed }}</p>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-amber">
                        <i data-lucide="clock" class="h-6 w-6"></i>
                    </div>
                    <span class="stat-badge badge-amber">Pending</span>
                </div>
                <h3 class="stat-label">Pending Approvals</h3>
                <p class="stat-value">{{ $pendingApprovals }}</p>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-rose">
                        <i data-lucide="briefcase" class="h-6 w-6"></i>
                    </div>
                    <span class="stat-badge badge-muted">Active</span>
                </div>
                <h3 class="stat-label">Total Departments</h3>
                <p class="stat-value">{{ $totalDepartments }}</p>
            </div>
        </div>


        <div class="stats-grid" style="grid-template-columns: repeat(5, 1fr);">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-amber">
                        <i data-lucide="shield-alert" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Security Alerts</h3>
                <p class="stat-value">{{ $securityAlerts }}</p>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="activity" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Active Sessions</h3>
                <p class="stat-value">{{ $activeSessions }}</p>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-rose">
                        <i data-lucide="user-x" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Failed Logins</h3>
                <p class="stat-value">{{ $failedLogins }}</p>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-indigo">
                        <i data-lucide="key-round" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Password Resets</h3>
                <p class="stat-value">{{ $passwordResets }}</p>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="shield-check" class="h-6 w-6"></i>
                    </div>
                    <span class="stat-badge badge-teal">Score</span>
                </div>
                <h3 class="stat-label">Security Score</h3>
                <p class="stat-value">{{ $securityScore }}/100</p>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="activity-container">
            <div class="activity-header">
                <h3 class="activity-title">Recent System Activity</h3>
                <button class="activity-link">
                    View History
                    <i data-lucide="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
            @if($recentActivities->count() > 0)
                <div class="activity-list">
                    @foreach($recentActivities as $activity)
                        <div class="activity-item">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <img src="{{ $activity->employee->photo_url }}" alt="" class="employee-avatar"
                                    style="width: 45px; height: 45px; border-radius: 12px; object-fit: cover; border: 1px solid var(--glass-border);">
                                <div>
                                    <p class="profile-name" style="margin: 0;">
                                        {{ $activity->employee->name ?? 'Unknown Employee' }}
                                    </p>
                                    <p class="profile-role" style="text-transform: none; font-size: 0.85rem; margin: 0;">Requested
                                        {{ $activity->leave_type }}
                                    </p>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span
                                    class="badge {{ $activity->status === 'Pending' ? 'badge-amber' : ($activity->status === 'Approved' ? 'badge-emerald' : 'badge-rose') }}">
                                    {{ $activity->status }}
                                </span>
                                <p class="activity-time"
                                    style="font-size: 0.75rem; color: var(--slate-500); margin-top: 0.35rem; margin-bottom: 0;">
                                    {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="activity-empty-state">
                    <div class="activity-empty-icon">
                        <i data-lucide="layout" class="h-12 w-12 text-slate-600"></i>
                    </div>
                    <h4 class="activity-empty-title">No activity found</h4>
                    <p class="activity-empty-desc">Detailed logs and payroll events will be displayed here as the system
                        processes employee data.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')@endsection