@extends('layouts.master')

@section('title', 'Security Incident Report')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/security_logs.css') }}">
    <style>
        .audit-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .audit-stat-card {
            background: var(--card-bg, #1e2130);
            border: 1px solid var(--border-color, #2e3347);
            border-radius: 0.75rem;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .audit-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .audit-stat-icon.total   { background: rgba(239,68,68,0.15); color: #f87171; }
        .audit-stat-icon.failed  { background: rgba(245,158,11,0.15);  color: #fb923c; }
        .audit-stat-icon.locked  { background: rgba(220,38,38,0.15);  color: #f87171; }
        .audit-stat-label { font-size: 0.75rem; color: var(--text-muted, #9ca3af); margin-bottom: 0.2rem; }
        .audit-stat-value { font-size: 1.5rem; font-weight: 700; color: var(--text-primary, #f1f5f9); }

        .action-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.65rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.04em;
        }
        .action-badge.failed   { background: rgba(239,68,68,0.15);  color: #f87171; }
        .action-badge.locked   { background: rgba(234,179,8,0.15);  color: #facc15; }
        .action-badge.default  { background: rgba(156,163,175,0.15);color: #9ca3af; }

        .btn-export-pdf {
            background: #dc2626;
            color: white;
            border: none;
            padding: 0.65rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-export-pdf:hover {
            background: #b91c1c;
            transform: translateY(-1px);
        }
    </style>
@endsection

@section('content')
<div class="govt-container">
    <div class="content-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 class="header-title">Security Incident Report</h2>
            <p class="header-subtitle">Comprehensive listing of authentication failures and locked account events</p>
        </div>
        <div>
            <a href="{{ route('it_admin.reports.security_incident.pdf', request()->all()) }}" class="btn-export-pdf">
                <i data-lucide="file-text" style="width:16px;height:16px;"></i>
                Export to PDF
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="audit-stats-grid">
        <div class="audit-stat-card">
            <div class="audit-stat-icon total">
                <i data-lucide="alert-triangle" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div class="audit-stat-label">Total Incidents</div>
                <div class="audit-stat-value">{{ number_format($totalIncidents) }}</div>
            </div>
        </div>
        <div class="audit-stat-card">
            <div class="audit-stat-icon failed">
                <i data-lucide="shield-alert" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div class="audit-stat-label">Failed Logins</div>
                <div class="audit-stat-value">{{ number_format($bruteForceAttempts) }}</div>
            </div>
        </div>
        <div class="audit-stat-card">
            <div class="audit-stat-icon locked">
                <i data-lucide="lock" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div class="audit-stat-label">Account Lockouts</div>
                <div class="audit-stat-value">{{ number_format($accountLockouts) }}</div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar-container">
        <form action="{{ route('it_admin.reports.security_incident') }}" method="GET" class="filter-bar-form">
            <div class="filter-search-wrapper">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search email, IP, or browser..." class="filter-input">
            </div>
            <div class="filter-date-wrapper" style="display: flex; gap: 0.5rem; align-items: center;">
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="filter-input" style="width: 140px;">
                <span style="color: var(--text-muted, #9ca3af); font-size: 0.8rem;">to</span>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="filter-input" style="width: 140px;">
            </div>
            <div class="filter-btn-group">
                <button type="submit" class="btn-filter-submit">Filter</button>
                <a href="{{ route('it_admin.reports.security_incident') }}" class="btn-filter-refresh">Reset</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="department-table-container">
        <table class="department-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Timestamp</th>
                    <th>Email Attempted</th>
                    <th>Incident Type</th>
                    <th>IP Address</th>
                    <th>Browser</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                    <tr>
                        <td>{{ $logs->firstItem() + $index }}</td>
                        <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                        <td>
                            <strong>{{ $log->email }}</strong><br>
                            @if($log->user)
                                <span class="text-muted" style="font-size: 0.8rem;">User: {{ $log->user->name }}</span>
                            @else
                                <span class="text-muted" style="font-size: 0.8rem; color: #ef4444;">Non-existent Account</span>
                            @endif
                        </td>
                        <td>
                            @if($log->status === 'FAILED')
                                <span class="action-badge failed">
                                    <i data-lucide="shield-alert" style="width:11px;height:11px;"></i>
                                    Login Failed
                                </span>
                            @elseif($log->status === 'LOCKED')
                                <span class="action-badge locked">
                                    <i data-lucide="lock" style="width:11px;height:11px;"></i>
                                    Account Locked
                                </span>
                            @else
                                <span class="action-badge default">
                                    <i data-lucide="activity" style="width:11px;height:11px;"></i>
                                    {{ $log->status }}
                                </span>
                            @endif
                        </td>
                        <td>{{ $log->ip_address }}</td>
                        <td>{{ $log->browser ?? 'Unknown' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">No security incidents detected in this period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $logs->links('vendor.pagination.numbers') }}
    </div>
</div>
@endsection
