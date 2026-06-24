@extends('layouts.master')

@section('title', 'Login Logs')

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
            <h2 class="header-title">Login Logs</h2>
            <p class="header-subtitle">Monitor user login attempts across the system</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success-log">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="filter-bar-container">
        <form action="{{ route('security_logs.login') }}" method="GET" class="filter-bar-form">
            
            <div class="filter-search-wrapper">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search email, IP, or reason..." class="filter-input">
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
                <a href="{{ route('security_logs.login') }}" class="btn-filter-refresh">Refresh Filters</a>
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
                    <th>Action</th>
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
                        <td style="display: flex; gap: 5px; align-items: center;">
                            <button type="button" class="department-action-link btn-view-log"
                                onclick="openLogModal({{ json_encode($log) }}, {{ json_encode($log->user ? $log->user->name : 'Unknown') }})">
                                View
                            </button>
                            
                            @if($log->user)
                                <form action="{{ route('security_logs.suspend') }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $log->user->id }}">
                                    @if($log->user->is_suspended)
                                        <button type="submit" class="department-action-link btn-unsuspend">
                                            Unsuspend
                                        </button>
                                    @else
                                        <button type="submit" class="department-action-link delete-link"
                                                onclick="return confirm('Are you sure you want to suspend this user? They will not be able to log in.')">
                                            Suspend
                                        </button>
                                    @endif
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-state">No login logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $logs->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Log Details Modal -->
<div id="logModal" class="modal-backdrop">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Login Log Details</h2>
            <button type="button" class="btn-close" onclick="closeLogModal()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="modal-inner">
            <div class="log-details-grid">
                <strong>Date & Time:</strong>
                <div id="modalDate"></div>
                
                <strong>User:</strong>
                <div id="modalUser"></div>
                
                <strong>Email:</strong>
                <div id="modalEmail"></div>
                
                <strong>IP Address:</strong>
                <div id="modalIp"></div>
                
                <strong>Browser:</strong>
                <div id="modalBrowser"></div>
                
                <strong>User Agent:</strong>
                <div id="modalUserAgent" class="text-muted" style="font-size: 0.8rem;"></div>
                
                <strong>Status:</strong>
                <div id="modalStatus"></div>
                
                <strong>Locked Until:</strong>
                <div id="modalLockedUntil"></div>
            </div>
            
            <form id="unlockForm" action="{{ route('security_logs.unlock') }}" method="POST" class="unlock-form-container">
                @csrf
                <input type="hidden" name="log_id" id="unlockLogId">
                <p class="unlock-alert-msg">
                    This account is currently locked due to too many failed login attempts from this IP.
                </p>
                <div class="unlock-action-row">
                    <button type="submit" class="btn-primary">Unlock Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openLogModal(log, userName) {
        document.getElementById('logModal').classList.add('show');
        
        // Format Date
        const dateObj = new Date(log.created_at);
        document.getElementById('modalDate').textContent = dateObj.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });
        
        document.getElementById('modalUser').textContent = userName;
        document.getElementById('modalEmail').textContent = log.email;
        document.getElementById('modalIp').textContent = log.ip_address;
        document.getElementById('modalBrowser').textContent = log.browser || 'Unknown';
        document.getElementById('modalUserAgent').textContent = log.user_agent;
        
        // Status Badge
        let statusHtml = '';
        if(log.status === 'SUCCESS') statusHtml = '<span class="badge badge-success">Success</span>';
        else if(log.status === 'FAILED') statusHtml = '<span class="badge badge-danger">Failed</span>';
        else if(log.status === 'LOCKED') statusHtml = '<span class="badge badge-warning">Locked</span>';
        else if(log.status === 'UNLOCKED') statusHtml = '<span class="badge badge-info">Unlocked</span>';
        else if(log.status === 'SUSPENDED') statusHtml = '<span class="badge badge-danger">Suspended</span>';
        document.getElementById('modalStatus').innerHTML = statusHtml;
        
        // Locked Until
        if(log.locked_until) {
            const lockedDate = new Date(log.locked_until);
            document.getElementById('modalLockedUntil').innerHTML = '<span class="locked-time-text">' + lockedDate.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true }) + '</span>';
        } else {
            document.getElementById('modalLockedUntil').innerHTML = '<span class="text-muted">N/A</span>';
        }
        
        // Unlock Form
        const unlockForm = document.getElementById('unlockForm');
        if (log.status === 'LOCKED') {
            document.getElementById('unlockLogId').value = log.id;
            unlockForm.style.display = 'block';
        } else {
            unlockForm.style.display = 'none';
        }
    }

    function closeLogModal() {
        document.getElementById('logModal').classList.remove('show');
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('logModal');
        if (e.target === modal) {
            closeLogModal();
        }
    });
</script>
@endsection
