@extends('it_admin.layouts.master')

@section('title', 'Audit Logs')

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
        .audit-stat-icon.failed  { background: rgba(239,68,68,0.15);  color: #f87171; }
        .audit-stat-icon.locked  { background: rgba(234,179,8,0.15);  color: #facc15; }
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
        .action-badge.failed   { background: rgba(239,68,68,0.15);  color: #f87171; }
        .action-badge.locked   { background: rgba(234,179,8,0.15);  color: #facc15; }
        .action-badge.unlocked { background: rgba(99,102,241,0.15); color: #818cf8; }
        .action-badge.default  { background: rgba(156,163,175,0.15);color: #9ca3af; }
    </style>
@endsection

@section('content')
<div class="govt-container">
    <div class="content-header">
        <div>
            <h2 class="header-title">Audit Logs</h2>
            <p class="header-subtitle">Complete audit trail of all system authentication events</p>
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

    {{-- Stats Cards --}}
    <div class="audit-stats-grid">
        <div class="audit-stat-card">
            <div class="audit-stat-icon total">
                <i data-lucide="scroll-text" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div class="audit-stat-label">Total Events</div>
                <div class="audit-stat-value">{{ number_format($totalEvents) }}</div>
            </div>
        </div>
        <div class="audit-stat-card">
            <div class="audit-stat-icon success">
                <i data-lucide="check-circle" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div class="audit-stat-label">Successful Logins</div>
                <div class="audit-stat-value">{{ number_format($successCount) }}</div>
            </div>
        </div>
        <div class="audit-stat-card">
            <div class="audit-stat-icon failed">
                <i data-lucide="x-circle" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div class="audit-stat-label">Failed Attempts</div>
                <div class="audit-stat-value">{{ number_format($failedCount) }}</div>
            </div>
        </div>
        <div class="audit-stat-card">
            <div class="audit-stat-icon locked">
                <i data-lucide="lock" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div class="audit-stat-label">Locked Accounts</div>
                <div class="audit-stat-value">{{ number_format($lockedCount) }}</div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar-container">
        <form action="{{ route('it_admin.audit_logs') }}" method="GET" class="filter-bar-form">
            <div class="filter-search-wrapper">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search email, IP, or browser..." class="filter-input">
            </div>
            <div class="filter-status-wrapper">
                <select name="action" class="filter-select">
                    <option value="">All Actions</option>
                    <option value="SUCCESS"  {{ request('action') === 'SUCCESS'  ? 'selected' : '' }}>Login Success</option>
                    <option value="FAILED"   {{ request('action') === 'FAILED'   ? 'selected' : '' }}>Login Failed</option>
                    <option value="LOCKED"   {{ request('action') === 'LOCKED'   ? 'selected' : '' }}>Account Locked</option>
                    <option value="UNLOCKED" {{ request('action') === 'UNLOCKED' ? 'selected' : '' }}>Account Unlocked</option>
                </select>
            </div>
            <div class="filter-date-wrapper">
                <input type="date" name="date" value="{{ request('date') }}" class="filter-input">
            </div>
            <div class="filter-btn-group">
                <button type="submit" class="btn-filter-submit">Filter</button>
                <a href="{{ route('it_admin.audit_logs') }}" class="btn-filter-refresh">Reset</a>
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
                    <th>User / Email</th>
                    <th>Action</th>
                    <th>IP Address</th>
                    <th>Browser</th>
                    <th>Locked Until</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditLogs as $index => $log)
                    <tr>
                        <td>{{ $auditLogs->firstItem() + $index }}</td>
                        <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                        <td>
                            @if($log->user)
                                <strong>{{ $log->user->name }}</strong><br>
                                <span class="text-muted">{{ $log->email }}</span>
                            @else
                                <strong>Unknown</strong><br>
                                <span class="text-muted">{{ $log->email }}</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $map = [
                                    'SUCCESS'  => ['cls' => 'success',  'label' => 'Login Success',     'icon' => 'check-circle'],
                                    'FAILED'   => ['cls' => 'failed',   'label' => 'Login Failed',      'icon' => 'x-circle'],
                                    'LOCKED'   => ['cls' => 'locked',   'label' => 'Account Locked',    'icon' => 'lock'],
                                    'UNLOCKED' => ['cls' => 'unlocked', 'label' => 'Account Unlocked',  'icon' => 'unlock'],
                                ];
                                $info = $map[$log->status] ?? ['cls' => 'default', 'label' => $log->status, 'icon' => 'activity'];
                            @endphp
                            <span class="action-badge {{ $info['cls'] }}">
                                <i data-lucide="{{ $info['icon'] }}" style="width:11px;height:11px;"></i>
                                {{ $info['label'] }}
                            </span>
                        </td>
                        <td>{{ $log->ip_address }}</td>
                        <td>{{ $log->browser ?? 'Unknown' }}</td>
                        <td>
                            @if($log->locked_until)
                                <span class="locked-time-text">{{ $log->locked_until->format('M d, Y h:i A') }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">No audit events found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $auditLogs->links('vendor.pagination.numbers') }}
    </div>
</div>
@endsection
