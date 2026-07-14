@extends('layouts.master')

@section('title', 'Pending Approvals - VIA Architects Associates')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/manage-employee.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/payroll.css') }}">
    <style>
        .approval-badge {
            display: inline-block;
            padding: 0.2rem 0.65rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-flagged { background: #fecaca; color: #7f1d1d; }

        .action-btn {
            display: inline-flex; align-items: center; gap: .3rem;
            padding: .28rem .7rem; border-radius: 5px;
            font-size: .72rem; font-weight: 600;
            cursor: pointer; border: none;
            transition: all .15s ease;
        }
        .btn-approve { background: #bbf7d0; color: #166534; }
        .btn-approve:hover { background: #4ade80; }
        .btn-flag    { background: #fee2e2; color: #7f1d1d; }
        .btn-flag:hover { background: #f87171; color: #fff; }

        .empty-state {
            text-align: center; padding: 4rem 2rem; color: #94a3b8;
        }
        .empty-state i { display: block; margin: 0 auto 1rem; opacity: .4; }

        .page-link-btn {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .4rem .85rem; border-radius: 6px;
            font-size: .82rem; font-weight: 600;
            border: 1px solid #e2e8f0; background: #fff; color: #475569;
            cursor: pointer; text-decoration: none;
            transition: all .15s ease;
        }
        .page-link-btn:hover { background: #f1f5f9; color: #1e293b; }
        .page-link-btn.active { background: #4f46e5; color: #fff; border-color: #4f46e5; }
        .pagination-bar { display: flex; align-items: center; gap: .5rem; justify-content: center; margin-top: 1.5rem; flex-wrap: wrap; }
    </style>
@endsection

@section('content')
<div class="max-w-full mx-auto px-6">

    {{-- Header --}}
    <div class="content-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
        <div>
            <h2 class="header-title">Pending Approvals</h2>
            <p class="header-subtitle">
                <span class="subtitle-dot"></span>
                Payroll runs awaiting your review and approval
            </p>
        </div>
        <a href="{{ route('finance_admin.payroll.create') }}" class="btn-primary" style="text-decoration:none;">
            <i data-lucide="plus" class="h-4 w-4"></i> Run Payroll
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem;">
            <i data-lucide="check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="payroll-summary-bar">
        <div class="summary-card">
            <div class="sc-label">Awaiting Approval</div>
            <div class="sc-value blue">{{ $payrolls->total() }}</div>
        </div>
        <div class="summary-card">
            <div class="sc-label">Total Gross Pay</div>
            <div class="sc-value blue">₱{{ number_format($payrolls->sum('gross_pay'), 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="sc-label">Total Net Pay</div>
            <div class="sc-value green">₱{{ number_format($payrolls->sum('net_pay'), 2) }}</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="employee-table-container">
        @if($payrolls->isEmpty())
            <div class="empty-state">
                <i data-lucide="check-circle-2" style="width:48px; height:48px;"></i>
                <h3 style="font-size:1.1rem; font-weight:600; color:#475569; margin-bottom:.5rem;">All Clear!</h3>
                <p style="font-size:.9rem;">No payroll runs are currently pending approval.</p>
            </div>
        @else
            <table class="employee-table" style="font-size:0.75rem;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Pay Period</th>
                        <th>Payroll Date</th>
                        <th>Basic Salary</th>
                        <th>Gross Pay</th>
                        <th>Deductions</th>
                        <th>Net Pay</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrolls as $i => $payroll)
                    @php
                        $emp  = $payroll->employee;
                        $name = $emp ? trim($emp->first_name . ' ' . $emp->last_name) : 'N/A';
                        $dept = $emp?->department?->department_name ?? '—';
                        $statusClass = $payroll->status === 'flagged' ? 'badge-flagged' : 'badge-pending';
                    @endphp
                    <tr>
                        <td>{{ $payrolls->firstItem() + $i }}</td>
                        <td>
                            <div class="employee-name-cell">
                                @if($emp)
                                    <img src="{{ $emp->photo_url }}" alt="" class="employee-avatar" style="object-fit:cover;">
                                @endif
                                <div>
                                    <p class="employee-name">{{ $name }}</p>
                                    <p style="font-size:.68rem; color:#94a3b8;">{{ $emp?->position?->position_name ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td>{{ $dept }}</td>
                        <td>
                            <span class="payroll-period-badge">
                                {{ date('M d', strtotime($payroll->payroll_period_start)) }} –
                                {{ date('M d, Y', strtotime($payroll->payroll_period_end)) }}
                            </span>
                        </td>
                        <td>{{ date('M d, Y', strtotime($payroll->payroll_date)) }}</td>
                        <td style="color:#0f172a;">₱{{ number_format($payroll->basic_salary, 2) }}</td>
                        <td style="color:#0f172a; font-weight:600;">₱{{ number_format($payroll->gross_pay, 2) }}</td>
                        <td style="color:#dc2626;">-₱{{ number_format($payroll->total_deductions, 2) }}</td>
                        <td style="color:#059669; font-weight:600;">₱{{ number_format($payroll->net_pay, 2) }}</td>
                        <td><span class="approval-badge {{ $statusClass }}">{{ ucfirst($payroll->status) }}</span></td>
                        <td style="white-space:nowrap;">
                            {{-- Approve --}}
                            <form method="POST" action="{{ route('finance_admin.payroll.approve', $payroll->payroll_id) }}"
                                  style="display:inline;">
                                @csrf
                                <button type="submit" class="action-btn btn-approve"
                                        onclick="return confirm('Approve this payroll run?')">
                                    <i data-lucide="check" class="h-3 w-3"></i> Approve
                                </button>
                            </form>

                            {{-- Flag --}}
                            <form method="POST" action="{{ route('finance_admin.payroll.flag', $payroll->payroll_id) }}"
                                  style="display:inline; margin-left:.35rem;">
                                @csrf
                                <button type="submit" class="action-btn btn-flag"
                                        onclick="return confirm('Flag this payroll run as discrepancy?')">
                                    <i data-lucide="flag" class="h-3 w-3"></i> Flag
                                </button>
                            </form>

                            {{-- Payslip --}}
                            <a href="{{ route('payroll.payslip.preview', ['payroll_id' => $payroll->payroll_id]) }}"
                               class="employee-action-link" style="margin-left:.35rem;">Payslip</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="pagination-bar">
                @if($payrolls->onFirstPage())
                    <span class="page-link-btn" style="opacity:.4; cursor:default;">&laquo; Prev</span>
                @else
                    <a class="page-link-btn" href="{{ $payrolls->previousPageUrl() }}">&laquo; Prev</a>
                @endif

                @foreach($payrolls->getUrlRange(1, $payrolls->lastPage()) as $page => $url)
                    @if($page == $payrolls->currentPage())
                        <span class="page-link-btn active">{{ $page }}</span>
                    @else
                        <a class="page-link-btn" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($payrolls->hasMorePages())
                    <a class="page-link-btn" href="{{ $payrolls->nextPageUrl() }}">Next &raquo;</a>
                @else
                    <span class="page-link-btn" style="opacity:.4; cursor:default;">Next &raquo;</span>
                @endif
            </div>
        @endif
    </div>

</div>
@endsection
