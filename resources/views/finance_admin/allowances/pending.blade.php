@extends('layouts.master')

@section('title', 'Pending Claims - VIA Architects Associates')

@section('styles')
<style>
    .claims-page { max-width: 1100px; margin: 0 auto; }
    .page-header { display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
    .page-title { font-size: 1.75rem; font-weight: 800; color: #1e293b; margin: 0; }
    .page-subtitle { font-size: 0.875rem; color: #64748b; margin-top: 0.25rem; }

    .count-pill {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: #fef3c7; color: #92400e; border: 1px solid #fde68a;
        border-radius: 999px; padding: 0.35rem 1rem; font-size: 0.85rem; font-weight: 700;
    }

    .panel {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .panel-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1.1rem 1.5rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc;
    }
    .panel-title { font-size: 1rem; font-weight: 700; color: #1e293b; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #f1f5f9; }
    th {
        padding: 0.75rem 1rem; text-align: left; font-size: 0.78rem; font-weight: 700;
        color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;
    }
    td {
        padding: 0.85rem 1rem; font-size: 0.875rem; color: #1e293b;
        border-bottom: 1px solid #f1f5f9; vertical-align: middle;
    }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #f8fafc; }

    .status-badge {
        display: inline-block; padding: 0.25rem 0.75rem; border-radius: 6px;
        font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
    }
    .status-pending { background: #fef3c7; color: #92400e; }

    .empty-state { padding: 3rem; text-align: center; color: #94a3b8; }
</style>
@endsection

@section('content')
<div class="claims-page">

    <div class="page-header">
        <div>
            <h2 class="page-title">Pending Claims</h2>
            <p class="page-subtitle">Leave and allowance requests awaiting finance processing</p>
        </div>
        <span class="count-pill">
            <i data-lucide="clock" style="width:14px;height:14px;"></i>
            {{ $claims->count() }} Pending
        </span>
    </div>

    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Pending Leave / Allowance Requests</span>
        </div>
        <div class="table-wrap">
            @if($claims->count())
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>Date From</th>
                        <th>Date To</th>
                        <th>Days</th>
                        <th>Filed On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($claims as $i => $claim)
                    <tr>
                        <td style="color:#94a3b8;">{{ $i + 1 }}</td>
                        <td style="font-weight:600;">
                            {{ $claim->employee->first_name ?? '' }} {{ $claim->employee->last_name ?? 'Unknown' }}
                        </td>
                        <td>{{ $claim->leave_type }}</td>
                        <td>{{ \Carbon\Carbon::parse($claim->start_date)->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($claim->end_date)->format('M d, Y') }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($claim->start_date)->diffInDays(\Carbon\Carbon::parse($claim->end_date)) + 1 }} day(s)
                        </td>
                        <td>{{ $claim->date_filed ? \Carbon\Carbon::parse($claim->date_filed)->format('M d, Y') : $claim->created_at->format('M d, Y') }}</td>
                        <td><span class="status-badge status-pending">Pending</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <i data-lucide="check-circle" style="width:3rem;height:3rem;margin:0 auto 0.75rem;display:block;opacity:0.2;"></i>
                <p>No pending claims at this time.</p>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
