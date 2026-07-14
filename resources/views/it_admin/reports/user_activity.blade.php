@extends('it_admin.layouts.master')

@section('title', 'User Activity Report')

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
        .audit-stat-icon.total   { background: rgba(99,102,241,0.15); color: #818cf8; }
        .audit-stat-icon.success { background: rgba(34,197,94,0.15);  color: #4ade80; }
        .audit-stat-icon.failed  { background: rgba(234,179,8,0.15);  color: #facc15; }
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
        .action-badge.success  { background: rgba(34,197,94,0.15);  color: #4ade80; }
        .action-badge.unlocked { background: rgba(99,102,241,0.15); color: #818cf8; }
        .action-badge.default  { background: rgba(156,163,175,0.15);color: #9ca3af; }

        .btn-export-pdf {
            background: #10b981;
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
            background: #059669;
            transform: translateY(-1px);
        }
    </style>
@endsection

@section('content')
<div class="govt-container">
    <div class="content-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 class="header-title">User Activity Report</h2>
            <p class="header-subtitle">Detailed history of login events and administrative account state changes</p>
        </div>
        <div>
            <a href="{{ route('it_admin.reports.user_activity.pdf', request()->all()) }}" class="btn-export-pdf">
                <i data-lucide="file-text" style="width:16px;height:16px;"></i>
                Export to PDF
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="audit-stats-grid">
        <div class="audit-stat-card">
            <div class="audit-stat-icon total">
                <i data-lucide="check-circle" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div class="audit-stat-label">Total Logins</div>
                <div class="audit-stat-value">{{ number_format($totalLogins) }}</div>
            </div>
        </div>
        <div class="audit-stat-card">
            <div class="audit-stat-icon success">
                <i data-lucide="users" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div class="audit-stat-label">Active Users</div>
                <div class="audit-stat-value">{{ number_format($uniqueUsers) }}</div>
            </div>
        </div>
        <div class="audit-stat-card">
            <div class="audit-stat-icon failed">
                <i data-lucide="unlock" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div class="audit-stat-label">Manual Unlocks</div>
                <div class="audit-stat-value">{{ number_format($totalUnlocks) }}</div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar-container">
        <form action="{{ route('it_admin.reports.user_activity') }}" method="GET" class="filter-bar-form">
            <div class="filter-search-wrapper">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search email, IP, or browser..." class="filter-input">
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center; flex-shrink: 0;">
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="filter-input" style="width: 150px; flex: none;">
                <span style="color: #64748b; font-size: 0.8rem; white-space: nowrap;">to</span>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="filter-input" style="width: 150px; flex: none;">
            </div>
            <div class="filter-btn-group" style="flex-shrink: 0;">
                <button type="submit" class="btn-filter-submit">Filter</button>
                <a href="{{ route('it_admin.reports.user_activity') }}" class="btn-filter-refresh">Reset</a>
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
                    <th>User Details</th>
                    <th>Action</th>
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
                            @if($log->user)
                                <strong>{{ $log->user->name }}</strong><br>
                                <span class="text-muted">{{ $log->email }}</span>
                            @else
                                <strong>Unknown User</strong><br>
                                <span class="text-muted">{{ $log->email }}</span>
                            @endif
                        </td>
                        <td>
                            @if($log->status === 'SUCCESS')
                                <span class="action-badge success">
                                    <i data-lucide="check-circle" style="width:11px;height:11px;"></i>
                                    Login Success
                                </span>
                            @elseif($log->status === 'UNLOCKED')
                                <span class="action-badge unlocked">
                                    <i data-lucide="unlock" style="width:11px;height:11px;"></i>
                                    Account Unlocked
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
                        <td colspan="6" class="empty-state">No activity logs found for this period.</td>
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
