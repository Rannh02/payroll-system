@extends('it_admin.layouts.master')

@section('title', 'Security Logs')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/security_logs.css') }}">
@endsection

@section('content')
<div class="govt-container">
    <div class="content-header">
        <div>
            <h2 class="header-title">Security Logs</h2>
            <p class="header-subtitle">Monitor user login activity for IT administration</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success-log">
            {{ session('success') }}
        </div>
    @endif

    <div class="filter-bar-container">
        <form action="{{ route('it_admin.security_logs') }}" method="GET" class="filter-bar-form">
            <div class="filter-search-wrapper">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search email, IP, or browser..." class="filter-input">
            </div>

            <div class="filter-status-wrapper">
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="SUCCESS" {{ request('status') === 'SUCCESS' ? 'selected' : '' }}>Success</option>
                    <option value="FAILED" {{ request('status') === 'FAILED' ? 'selected' : '' }}>Failed</option>
                    <option value="LOCKED" {{ request('status') === 'LOCKED' ? 'selected' : '' }}>Locked</option>
                    <option value="UNLOCKED" {{ request('status') === 'UNLOCKED' ? 'selected' : '' }}>Unlocked</option>
                    <option value="SUSPENDED" {{ request('status') === 'SUSPENDED' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <div class="filter-date-wrapper">
                <input type="date" name="date" value="{{ request('date') }}" class="filter-input">
            </div>

            <div class="filter-btn-group">
                <button type="submit" class="btn-filter-submit">Filter Logs</button>
                <a href="{{ route('it_admin.security_logs') }}" class="btn-filter-refresh">Refresh Filters</a>
            </div>
        </form>
    </div>

    <div class="department-table-container">
        <table class="department-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date & Time</th>
                    <th>User</th>
                    <th>IP Address</th>
                    <th>Browser</th>
                    <th>Status</th>
                    <th>Locked Until</th>
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
                                <strong>Unknown</strong><br>
                                <span class="text-muted">{{ $log->email }}</span>
                            @endif
                        </td>
                        <td>{{ $log->ip_address }}</td>
                        <td>{{ $log->browser ?? 'Unknown' }}</td>
                        <td>
                            @if($log->status === 'SUCCESS')
                                <span class="badge badge-success">Success</span>
                            @elseif($log->status === 'FAILED')
                                <span class="badge badge-danger">Failed</span>
                            @elseif($log->status === 'LOCKED')
                                <span class="badge badge-warning">Locked</span>
                            @elseif($log->status === 'UNLOCKED')
                                <span class="badge badge-info">Unlocked</span>
                            @elseif($log->status === 'SUSPENDED')
                                <span class="badge badge-danger">Suspended</span>
                            @endif
                        </td>
                        <td>
                            @if($log->locked_until)
                                <span class="locked-time-text">{{ $log->locked_until->format('h:i A') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">No login logs found.</td>
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