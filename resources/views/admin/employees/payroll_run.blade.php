@extends('layouts.master')

@section('title', 'Payroll Run - VIA Architects Associates')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/manage-employee.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/payroll.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('js/admin/employees/payroll_run.js') }}"></script>
@endsection

@section('content')
<div class="max-w-full mx-auto px-6">

    {{-- ── Header ── --}}
    <div class="content-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
        <div>
            <h2 class="header-title">Payroll Run</h2>
            <p class="header-subtitle">
                <span class="subtitle-dot"></span>
                Active Employees &mdash; All Departments
            </p>
        </div>
        {{-- Period filter form --}}
        <form method="GET" action="{{ route('payroll.index') }}"
              style="display:flex; gap:.6rem; align-items:center; flex-wrap:wrap;">
            <label style="font-size:.8rem; font-weight:600; color:#64748b;">Period:</label>
            <input type="date" name="from" value="{{ request('from', now()->startOfMonth()->toDateString()) }}"
                   class="form-input" style="padding:.35rem .6rem; font-size:.82rem; width:140px;">
            <span style="font-size:.8rem; color:#94a3b8;">to</span>
            <input type="date" name="to" value="{{ request('to', now()->endOfMonth()->toDateString()) }}"
                   class="form-input" style="padding:.35rem .6rem; font-size:.82rem; width:140px;">
            <button type="submit" class="btn-primary" style="padding:.38rem .9rem; font-size:.82rem;">
                <i data-lucide="refresh-cw" class="h-4 w-4"></i> Load
            </button>
        </form>
    </div>

    {{-- ── Flash Messages ── --}}
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem;">
            <i data-lucide="check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- ── Summary Cards ── --}}
    @php
        $totalGross     = $payrolls->sum('gross_pay');
        $totalDeductions= $payrolls->sum('total_deductions');
        $totalNet       = $payrolls->sum('net_pay');
        $totalEmployees = $employees->count();
    @endphp

    <div class="payroll-summary-bar">
        <div class="summary-card">
            <div class="sc-label">Employees</div>
            <div class="sc-value blue">{{ $totalEmployees }}</div>
        </div>
        <div class="summary-card">
            <div class="sc-label">Total Gross Pay</div>
            <div class="sc-value blue">₱{{ number_format($totalGross, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="sc-label">Total Deductions</div>
            <div class="sc-value red">-₱{{ number_format($totalDeductions, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="sc-label">Total Net Pay</div>
            <div class="sc-value green">₱{{ number_format($totalNet, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="sc-label">Payroll Records</div>
            <div class="sc-value blue">{{ $payrolls->count() }}</div>
        </div>
    </div>

    {{-- ── Search & Department Filter ── --}}
    <div class="employee-search-container">
        <div class="employee-search-input-wrapper">
            <input type="text" id="payrollSearch" placeholder="Search employees..."
                   class="employee-search-input" oninput="filterTable()" />
        </div>
        <div class="employee-department-select">
            <select id="deptFilter" onchange="filterTable()">
                <option value="">All Departments</option>
                @foreach($employees->pluck('department.department_name')->filter()->unique()->sort() as $dept)
                    <option value="{{ $dept }}">{{ $dept }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ── Payroll Records Table ── --}}
    <div class="employee-table-container">
        @if(!$employees->count())
        <div class="empty-payroll">
            <i data-lucide="inbox" style="width:40px; height:40px; margin-bottom:.75rem; opacity:.4;"></i>
            <p style="font-weight:600; margin-bottom:.25rem;">No active employees found.</p>
            <p>Please add employees first before running payroll.</p>
        </div>
        @else
        <table class="employee-table" id="payrollTable" style="font-size: 0.75rem;">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Period</th>
                    <th>Basic Salary</th>
                    <th>OT Pay</th>
                    <th>Gross Pay</th>
                    <th>SSS</th>
                    <th>PhilHealth</th>
                    <th>Pag-IBIG</th>
                    <th>Tax</th>
                    <th>Total DED</th>
                    <th>Net Pay</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $emp)
                @php
                    $payroll = $payrolls->where('employee_id', $emp->employee_id)->first();
                    $name  = trim($emp->first_name . ' ' . $emp->last_name);
                    $dept  = $emp->department?->department_name ?? '—';
                    $init  = strtoupper(substr($name, 0, 1));
                    $fromVal = request('from', now()->startOfMonth()->toDateString());
                    $toVal   = request('to', now()->endOfMonth()->toDateString());
                    $period= $payroll 
                           ? date('M d', strtotime($payroll->payroll_period_start)) . ' – ' . date('M d, Y', strtotime($payroll->payroll_period_end))
                           : date('M d', strtotime($fromVal)) . ' – ' . date('M d, Y', strtotime($toVal));
                @endphp
                <tr class="payroll-row" data-name="{{ strtolower($name) }}" data-dept="{{ $dept }}">
                    <td class="employee-id">
                        <div class="employee-name-cell">
                            <img src="{{ $emp->photo_url }}" alt="" class="employee-avatar" style="object-fit: cover;">
                            <div>
                                <p class="employee-name">{{ $name }}</p>
                                <p style="font-size:.68rem; color:#94a3b8;">{{ $emp->position?->position_name ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td>{{ $dept }}</td>
                    <td><span class="payroll-period-badge">{{ $period }}</span></td>
                    @if($payroll)
                        <td style="color:#0f172a;">₱{{ number_format($payroll->basic_salary, 2) }}</td>
                        <td style="color:#0f172a;">₱{{ number_format($payroll->overtime_pay ?? 0, 2) }}</td>
                        <td style="color:#0f172a; font-weight:600;">₱{{ number_format($payroll->gross_pay, 2) }}</td>
                        <td style="color:#dc2626;">-₱{{ number_format($payroll->sss ?? 0, 2) }}</td>
                        <td style="color:#dc2626;">-₱{{ number_format($payroll->philhealth ?? 0, 2) }}</td>
                        <td style="color:#dc2626;">-₱{{ number_format($payroll->hdmf ?? 0, 2) }}</td>
                        <td style="color:#dc2626;">-₱{{ number_format($payroll->tax ?? 0, 2) }}</td>
                        <td style="color:#dc2626;">-₱{{ number_format($payroll->total_deductions, 2) }}</td>
                        <td style="color:#059669; font-weight:600;">₱{{ number_format($payroll->net_pay, 2) }}</td>
                        <td>
                            <a href="/payroll/payslip-preview?payroll_id={{ $payroll->payroll_id }}" class="employee-action-link">Payslip</a>
                        </td>
                    @else
                        <td colspan="9" style="text-align: center;">
                            <form method="POST" action="{{ route('payroll.run', ['employee' => $emp->employee_id, 'from' => $fromVal, 'to' => $toVal]) }}" style="margin: 0; display: inline-block;">
                                @csrf
                                <button type="submit" class="btn-primary" style="padding:.3rem 1rem; font-size:.75rem; border-radius:.375rem; font-weight:600;">
                                    Run Payroll
                                </button>
                            </form>
                        </td>
                    @endif
                </tr>
                @endforeach
 
                @if($payrolls->count() > 0)
                {{-- Totals row --}}
                <tr style="background:#f1f5f9; font-weight:600;">
                    <td colspan="3" style="padding:1rem 1.5rem; border-right:1px solid #cbd5e1; color:#0f172a;">TOTALS</td>
                    <td style="padding:1rem 1.5rem; border-right:1px solid #cbd5e1; color:#0f172a;">₱{{ number_format($payrolls->sum('basic_salary'), 2) }}</td>
                    <td style="padding:1rem 1.5rem; border-right:1px solid #cbd5e1; color:#0f172a;">₱{{ number_format($payrolls->sum('overtime_pay'), 2) }}</td>
                    <td style="padding:1rem 1.5rem; border-right:1px solid #cbd5e1; color:#0f172a;">₱{{ number_format($totalGross, 2) }}</td>
                    <td style="padding:1rem 1.5rem; border-right:1px solid #cbd5e1; color:#dc2626;">-₱{{ number_format($payrolls->sum('sss'), 2) }}</td>
                    <td style="padding:1rem 1.5rem; border-right:1px solid #cbd5e1; color:#dc2626;">-₱{{ number_format($payrolls->sum('philhealth'), 2) }}</td>
                    <td style="padding:1rem 1.5rem; border-right:1px solid #cbd5e1; color:#dc2626;">-₱{{ number_format($payrolls->sum('hdmf'), 2) }}</td>
                    <td style="padding:1rem 1.5rem; border-right:1px solid #cbd5e1; color:#dc2626;">-₱{{ number_format($payrolls->sum('tax'), 2) }}</td>
                    <td style="padding:1rem 1.5rem; border-right:1px solid #cbd5e1; color:#dc2626;">-₱{{ number_format($totalDeductions, 2) }}</td>
                    <td style="padding:1rem 1.5rem; border-right:1px solid #cbd5e1; color:#059669;">₱{{ number_format($totalNet, 2) }}</td>
                    <td style="padding:1rem 1.5rem;"></td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif
    </div>

</div>
@endsection

