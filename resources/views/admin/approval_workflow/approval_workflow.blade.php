@extends('layouts.master')

@section('title', 'Approval Workflow - VIA Architects Associates')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/manage-employee.css') }}">
@endsection

@section('content')
    <div class="max-w-[1600px] mx-auto">
        <div class="content-header">
            <div>
                <h2 class="header-title">Approval Workflow</h2>
                <p class="header-subtitle">
                    <span class="subtitle-dot"></span>
                    Review and manage employee leave requests
                </p>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert-success">
                <i data-lucide="check-circle"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Leave Requests Table --}}
        <div class="approval-table-container">
            <table class="approval-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Date Filed</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveRequests as $index => $leave)
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="employee-name-cell">
                                    <img src="{{ $leave->employee->photo_url }}" alt="" class="employee-avatar" style="object-fit: cover;">
                                    <div>
                                        <div class="font-semibold text-main">
                                            {{ $leave->employee->first_name ?? '—' }} {{ $leave->employee->last_name ?? '' }}
                                        </div>
                                        <div class="text-xs text-muted">
                                            {{ $leave->employee->position->position_name ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $leave->employee->department->department_name ?? '—' }}</td>
                            <td>
                                <span class="leave-badge badge-leave-type">
                                    {{ ucfirst($leave->leave_type) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</td>
                            <td class="text-muted">{{ \Carbon\Carbon::parse($leave->date_filed)->format('M d, Y') }}</td>
                            <td class="max-w-180">
                                <span title="{{ $leave->reason }}" class="truncate-text">
                                    {{ $leave->reason ?: '—' }}
                                </span>
                            </td>
                            <td>
                                @if($leave->status === 'pending')
                                    <span class="leave-badge badge-pending">Pending</span>
                                @elseif($leave->status === 'approved')
                                    <span class="leave-badge badge-approved">Approved</span>
                                @else
                                    <span class="leave-badge badge-rejected">Rejected</span>
                                @endif
                            </td>
                            <td>
                                <div class="employee-action-group">
                                    @if($leave->status === 'pending')
                                        <button type="button" class="employee-action-link action-approve"
                                            onclick="openApproveModal('{{ route('approval_workflow.status', $leave->leave_request_id) }}', '{{ $leave->employee->first_name }} {{ $leave->employee->last_name }}')">
                                            Approve
                                        </button>
                                        
                                        <button type="button" class="employee-action-link action-archive"
                                            onclick="openRejectModal('{{ route('approval_workflow.status', $leave->leave_request_id) }}', '{{ $leave->employee->first_name }} {{ $leave->employee->last_name }}')">
                                            Reject
                                        </button>
                                    @else
                                        <span class="text-xs text-muted italic">Processed</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="empty-table-msg">
                                No leave requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Approve Confirmation Modal -->
    <div id="approve-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-inner">
                <div class="modal-top">
                    <div class="modal-icon-container icon-success">
                        <i data-lucide="check-circle" class="h-6 w-6"></i>
                    </div>
                    <div class="modal-info">
                        <h3 class="modal-title">Approve Leave Request</h3>
                        <p class="modal-description">
                            Are you sure you want to approve the leave request for <strong id="approve-employee-name" class="font-bold"></strong>? 
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal btn-modal-secondary" onclick="closeApproveModal()">Cancel</button>
                <form id="approve-form" method="POST" style="margin: 0;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="btn-modal btn-modal-success">Approve Request</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <div id="reject-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-inner">
                <div class="modal-top">
                    <div class="modal-icon-container">
                        <i data-lucide="alert-triangle" class="h-6 w-6"></i>
                    </div>
                    <div class="modal-info">
                        <h3 class="modal-title">Reject Leave Request</h3>
                        <p class="modal-description">
                            Are you sure you want to reject the leave request for <strong id="reject-employee-name" class="font-bold"></strong>?
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal btn-modal-secondary" onclick="closeRejectModal()">Cancel</button>
                <form id="reject-form" method="POST" style="margin: 0;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" class="btn-modal btn-modal-danger">Reject Request</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function openApproveModal(actionUrl, employeeName) {
            document.getElementById('approve-form').action = actionUrl;
            document.getElementById('approve-employee-name').textContent = employeeName;
            document.getElementById('approve-modal').classList.add('show');
        }

        function closeApproveModal() {
            document.getElementById('approve-modal').classList.remove('show');
        }

        function openRejectModal(actionUrl, employeeName) {
            document.getElementById('reject-form').action = actionUrl;
            document.getElementById('reject-employee-name').textContent = employeeName;
            document.getElementById('reject-modal').classList.add('show');
        }

        function closeRejectModal() {
            document.getElementById('reject-modal').classList.remove('show');
        }
        
        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const approveModal = document.getElementById('approve-modal');
            const rejectModal = document.getElementById('reject-modal');
            if (event.target === approveModal) closeApproveModal();
            if (event.target === rejectModal) closeRejectModal();
        });
    </script>
@endsection