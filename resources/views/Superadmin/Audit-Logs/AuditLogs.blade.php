@extends('Superadmin.layouts.master')

@section('title', 'Audit Logs')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/department.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/security_logs.css') }}">
@endsection

@section('content')
<div class="govt-container">
    <div class="content-header">
        <div>
            <h2 class="header-title">Audit Logs</h2>
            <p class="header-subtitle">Monitor system actions performed by users across the system</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success-log">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="filter-bar-container">
        <form action="{{ route('superadmin.AuditLogs') }}" method="GET" class="filter-bar-form">
            
            <div class="filter-search-wrapper">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user, role, action, or IP..." class="filter-input">
            </div>

            <div class="filter-status-wrapper">
                <select name="role" class="filter-select">
                    <option value="">All Roles</option>
                    <option value="HR Admin" {{ request('role') === 'HR Admin' ? 'selected' : '' }}>HR Admin</option>
                    <option value="IT Admin" {{ request('role') === 'IT Admin' ? 'selected' : '' }}>IT Admin</option>
                    <option value="Finance Admin" {{ request('role') === 'Finance Admin' ? 'selected' : '' }}>Finance Admin</option>
                    <option value="Employee" {{ request('role') === 'Employee' ? 'selected' : '' }}>Employee</option>
                    <option value="Superadmin" {{ request('role') === 'Superadmin' ? 'selected' : '' }}>Superadmin</option>
                </select>
            </div>

            <div class="filter-date-wrapper">
                <input type="date" name="date" value="{{ request('date') }}" class="filter-input">
            </div>

            <div class="filter-btn-group">
                <button type="submit" class="btn-filter-submit">Filter Logs</button>
                <a href="{{ route('superadmin.AuditLogs') }}" class="btn-filter-refresh">Refresh Filters</a>
            </div>
        </form>
    </div>

    <div class="department-table-container">
        <table class="department-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date & Time</th>
                    <th>User Name</th>
                    <th>Role</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                    <th>Browser</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                    <tr>
                        <td>{{ $logs->firstItem() + $index }}</td>
                        <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                        <td><strong>{{ $log->user_name }}</strong></td>
                        <td>
                            @php
                                $role = $log->role;
                                $roleLabel = match(strtolower($role)) {
                                    'superadmin'    => ['label' => 'Super Admin',  'class' => 'badge-purple'],
                                    'admin'         => ['label' => 'Admin',        'class' => 'badge-info'],
                                    'hr admin'      => ['label' => 'HR',           'class' => 'badge-teal'],
                                    'hr'            => ['label' => 'HR',           'class' => 'badge-teal'],
                                    'it admin'      => ['label' => 'IT Admin',     'class' => 'badge-dark'],
                                    'it_admin'      => ['label' => 'IT Admin',     'class' => 'badge-dark'],
                                    'finance admin' => ['label' => 'Finance',      'class' => 'badge-warning'],
                                    'finance_admin' => ['label' => 'Finance',      'class' => 'badge-warning'],
                                    'employee'      => ['label' => 'Employee',     'class' => 'badge-secondary'],
                                    default         => ['label' => ucfirst(str_replace('_', ' ', $role)), 'class' => 'badge-secondary'],
                                };
                            @endphp
                            <span class="badge {{ $roleLabel['class'] ?? 'badge-secondary' }}">{{ $roleLabel['label'] ?? ucfirst($role) }}</span>
                        </td>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->description ?? '-' }}</td>
                        <td>{{ $log->ip_address }}</td>
                        <td>{{ $log->browser ?? 'Unknown' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-state">No audit logs found.</td>
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