@extends('layouts.master')

@section('title', 'My Requests - VIA Architects Associates')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/manage-employee.css') }}">
@endsection

@section('content')
    <div class="max-w-[1600px] mx-auto">
        <div class="content-header">
            <div>
                <h2 class="header-title">My Requests</h2>
                <p class="header-subtitle">
                    <span class="subtitle-dot"></span>
                    Track the status of your submitted leave requests
                </p>
            </div>
            <div class="header-actions">
                <a href="{{ route('user.leave_form') }}" class="btn-primary">
                    <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                    New Request
                </a>
            </div>
        </div>

        <div class="approval-table-container">
            <table class="approval-table">
                <thead>
                    <tr>
                        <th>Date Filed</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveRequests as $leave)
                        <tr>
                            <td style="color: #64748b;">{{ \Carbon\Carbon::parse($leave->date_filed)->format('M d, Y') }}</td>
                            <td>
                                <span class="leave-badge badge-leave-type">
                                    {{ ucfirst($leave->leave_type) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</td>
                            <td style="max-width: 300px;">
                                <span title="{{ $leave->reason }}"
                                    style="display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 3rem; text-align: center; color: #64748b;">
                                <i data-lucide="inbox" class="h-12 w-12 mx-auto mb-3 opacity-20"></i>
                                <p>You haven't submitted any requests yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
