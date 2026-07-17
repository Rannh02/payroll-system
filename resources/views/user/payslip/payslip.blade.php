@extends('layouts.master')

@section('title', 'Employee Payslip')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/user/style.css') }}">
    <style>
        /* ── Layout ──────────────────────────────────────── */
        .ps-wrapper {
            max-width: 860px;
            margin: 2rem auto;
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        /* ── Action Bar (top) ────────────────────────────── */
        .ps-action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            gap: 0.75rem;
        }
        .ps-action-bar .ps-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 1rem;
            border-radius: 0.5rem;
            background: rgba(255,255,255,0.05);
            color: #94a3b8;
            border: 1px solid rgba(255,255,255,0.08);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.2s;
        }
        .ps-action-bar .ps-back-btn:hover { background: rgba(255,255,255,0.1); }
        .ps-action-bar .ps-btn-group { display: flex; gap: 0.6rem; }

        .ps-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.5rem 1.15rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: filter 0.2s, transform 0.15s;
        }
        .ps-btn:hover { filter: brightness(1.12); transform: translateY(-1px); }
        .ps-btn-print  { background: #3b82f6; color: #fff; }
        .ps-btn-pdf    { background: linear-gradient(135deg, #10b981, #059669); color: #fff; box-shadow: 0 4px 14px rgba(16,185,129,0.35); }

        /* ── Main Card ───────────────────────────────────── */
        .ps-card {
            background: var(--bg-surface, #1e293b);
            border: 1px solid var(--glass-border, rgba(255,255,255,0.07));
            border-radius: 1.25rem;
            overflow: hidden;
            box-shadow: 0 20px 40px -8px rgba(0,0,0,0.25);
        }

        /* ── Header Band ─────────────────────────────────── */
        .ps-header {
            padding: 1.75rem 2rem 1.5rem;
            border-bottom: 3px solid #1d4ed8;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .ps-company-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: #3b82f6;
            letter-spacing: -0.3px;
        }
        .ps-company-sub {
            font-size: 0.78rem;
            color: #64748b;
            margin-top: 3px;
        }
        .ps-title-block { text-align: right; }
        .ps-title {
            font-size: 1.75rem;
            font-weight: 900;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--text-main, #f8fafc);
        }
        .ps-period {
            font-size: 0.78rem;
            color: #94a3b8;
            margin-top: 4px;
        }

        /* ── Employee Info Band ──────────────────────────── */
        .ps-emp-band {
            background: rgba(255,255,255,0.03);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            padding: 1.1rem 2rem;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }
        .ps-emp-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #64748b;
            margin-bottom: 4px;
        }
        .ps-emp-value {
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--text-main, #f8fafc);
        }

        /* ── Body padding ────────────────────────────────── */
        .ps-body { padding: 1.5rem 2rem 2rem; }

        /* ── Section header ──────────────────────────────── */
        .ps-sec-header {
            background: #1d4ed8;
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 0.45rem 0.85rem;
            border-radius: 0.35rem 0.35rem 0 0;
            margin-bottom: 0;
        }

        /* ── Generic data table ──────────────────────────── */
        .ps-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
            border-radius: 0 0 0.5rem 0.5rem;
            overflow: hidden;
        }
        .ps-table th {
            background: rgba(226,232,240,0.07);
            color: #64748b;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 0.55rem 0.85rem;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .ps-table th.r, .ps-table td.r { text-align: right; }
        .ps-table td {
            padding: 0.6rem 0.85rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            color: var(--text-main, #f8fafc);
        }
        .ps-table tr:last-child td { border-bottom: none; }
        .ps-table tr.even td { background: rgba(255,255,255,0.02); }
        .ps-table tr.ps-total td {
            font-weight: 700;
            background: rgba(255,255,255,0.04);
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .ps-table tr.ps-total td:last-child { color: #3b82f6; }

        /* ── Two-column grid ─────────────────────────────── */
        .ps-two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }

        /* ── Government table (full-width) ───────────────── */
        .ps-table .text-red   { color: #f87171; }
        .ps-table .text-green { color: #34d399; }
        .ps-table .text-blue  { color: #60a5fa; }

        /* ── Final computation box ───────────────────────── */
        .ps-computation {
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 0.5rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
            max-width: 380px;
        }
        .ps-comp-row {
            display: flex;
            justify-content: space-between;
            padding: 0.3rem 0;
            font-size: 0.875rem;
            color: #94a3b8;
        }
        .ps-comp-row strong { color: var(--text-main, #f8fafc); }
        .ps-comp-row.cr-deduct strong { color: #f87171; }
        .ps-comp-divider {
            border: none;
            border-top: 2px solid #1d4ed8;
            margin: 0.5rem 0;
        }
        .ps-comp-total {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0 0;
            font-size: 1.05rem;
            font-weight: 800;
        }
        .ps-comp-total span:last-child { color: #3b82f6; }

        /* ── Net Pay Banner ──────────────────────────────── */
        .ps-net-banner {
            background: linear-gradient(135deg, #1d4ed8 0%, #0ea5e9 100%);
            border-radius: 0.75rem;
            padding: 1.5rem 2rem;
            text-align: center;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 24px rgba(29,78,216,0.35);
        }
        .ps-net-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255,255,255,0.75);
            margin-bottom: 0.3rem;
        }
        .ps-net-amount {
            font-size: 2.5rem;
            font-weight: 900;
            color: #fff;
            letter-spacing: -0.5px;
        }
        .ps-net-sub {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.6);
            margin-top: 0.3rem;
        }

        /* ── Signature section ───────────────────────────── */
        .ps-sig-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.07);
            padding-top: 1.5rem;
        }
        .ps-sig-label {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 2.5rem;
        }
        .ps-sig-line {
            border-top: 1.5px solid rgba(255,255,255,0.2);
            padding-top: 0.5rem;
        }
        .ps-sig-name {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-main, #f8fafc);
        }
        .ps-sig-role {
            font-size: 0.72rem;
            color: #64748b;
        }

        /* ── Error state ─────────────────────────────────── */
        .ps-error {
            text-align: center;
            padding: 3rem 2rem;
        }
        .ps-error-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px; height: 64px;
            border-radius: 50%;
            background: rgba(239,68,68,0.1);
            color: #ef4444;
            margin-bottom: 1.25rem;
        }
        .ps-error h2 { font-size: 1.4rem; font-weight: 700; color: var(--text-main, #f8fafc); margin-bottom: 0.5rem; }
        .ps-error p  { color: #94a3b8; max-width: 420px; margin: 0 auto 2rem; font-size: 0.9rem; line-height: 1.5; }

        /* ── Pending approval state ────────────────────────── */
        .ps-pending-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px; height: 72px;
            border-radius: 50%;
            background: rgba(245,158,11,0.12);
            color: #f59e0b;
            margin-bottom: 1.25rem;
            animation: pulse-ring 2s ease-in-out infinite;
        }
        @keyframes pulse-ring {
            0%, 100% { box-shadow: 0 0 0 0 rgba(245,158,11,0.25); }
            50%       { box-shadow: 0 0 0 10px rgba(245,158,11,0); }
        }
        .ps-pending-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(245,158,11,0.12);
            color: #f59e0b;
            border: 1px solid rgba(245,158,11,0.25);
            border-radius: 999px;
            padding: 0.3rem 0.9rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1.5rem;
        }

        @media print {
            .ps-action-bar { display: none; }
        }
    </style>
@endsection

@section('content')
<div class="ps-wrapper">

    @if(isset($error))
        {{-- ── Error / Pending State ── --}}
        <div class="ps-card">
            <div class="ps-error">
                @if(str_contains($error, 'reviewed') || str_contains($error, 'not been approved'))
                    {{-- Pending Approval State --}}
                    <div class="ps-pending-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div class="ps-pending-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Pending Approval
                    </div>
                    <h2>Payslip Awaiting Finance Admin Approval</h2>
                    <p>{{ $error }}</p>
                @else
                    {{-- No Payroll Found State --}}
                    <div class="ps-error-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <h2>No Payslip Available</h2>
                    <p>{{ $error }}</p>
                @endif
                <a href="{{ route('user.dashboard') }}" class="ps-btn ps-btn-print">
                    Back to Dashboard
                </a>
            </div>
        </div>
    @else

        {{-- ── Action Bar ── --}}
        <div class="ps-action-bar">
            <a href="{{ Auth::guard('admin')->check() ? route('payroll.index') : route('user.dashboard') }}" class="ps-back-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Back
            </a>
            <div class="ps-btn-group">
                @if($payroll)
                    @if(Auth::guard('admin')->check())
                        <a href="{{ route('payroll.download_pdf', ['payroll' => $payroll->payroll_id]) }}" class="ps-btn ps-btn-pdf">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Download PDF
                        </a>
                    @else
                        <a href="{{ route('user.payslip.download_pdf', ['payroll' => $payroll->payroll_id]) }}" class="ps-btn ps-btn-pdf">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Download PDF
                        </a>
                    @endif
                @endif
                <button onclick="window.print()" class="ps-btn ps-btn-print">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Print
                </button>
            </div>
        </div>

        {{-- ── Payslip Card ── --}}
        <div class="ps-card">

            {{-- Header --}}
            <div class="ps-header">
                <div>
                    <div class="ps-company-name">VIA Architects Associates</div>
                    <div class="ps-company-sub">Human Resources &amp; Payroll Department</div>
                </div>
                <div class="ps-title-block">
                    <div class="ps-title">Payslip</div>
                    <div class="ps-period">
                        Period: {{ \Carbon\Carbon::parse($payroll->payroll_period_start)->format('M d, Y') }}
                        &ndash; {{ \Carbon\Carbon::parse($payroll->payroll_period_end)->format('M d, Y') }}
                    </div>
                    <div class="ps-period">
                        Pay Date: {{ \Carbon\Carbon::parse($payroll->payroll_date)->format('M d, Y') }}
                    </div>
                </div>
            </div>

            {{-- Employee Info Band --}}
            <div class="ps-emp-band">
                <div>
                    <div class="ps-emp-label">Employee Name</div>
                    <div class="ps-emp-value">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                </div>
                <div>
                    <div class="ps-emp-label">Employee ID</div>
                    <div class="ps-emp-value">{{ $employee->employee_number ?? ('VIA-' . date('Y') . '-' . str_pad($employee->employee_id, 3, '0', STR_PAD_LEFT)) }}</div>
                </div>
                <div>
                    <div class="ps-emp-label">Position</div>
                    <div class="ps-emp-value">{{ $employee->position->position_name ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="ps-emp-label">Department</div>
                    <div class="ps-emp-value">{{ $employee->department->department_name ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="ps-body">

                @php
                    $monthlySalary = $employee->salary_rate ?? ($employee->position->basic_salary ?? 0);
                    $dailyRate     = $monthlySalary > 0 ? $monthlySalary / 22 : 0;
                    $hourlyRate    = $dailyRate / 8;
                    $minuteRate    = $hourlyRate / 60;
                    $absenceDeduct = $dailyRate * $payroll->absent_days;
                    $lateDeduct    = $minuteRate * $payroll->late_minutes;
                    $underDeduct   = $minuteRate * $payroll->undertime_minutes;
                    $attTotal      = $absenceDeduct + $lateDeduct + $underDeduct;
                    $otHours       = ($payroll->overtime_pay > 0 && $monthlySalary > 0)
                                     ? $payroll->overtime_pay / ($hourlyRate * 1.25)
                                     : 0;
                @endphp

                {{-- Two-column: Salary Summary + Attendance --}}
                <div class="ps-two-col">
                    <div>
                        <div class="ps-sec-header">Salary Summary</div>
                        <table class="ps-table">
                            <tr><td>Monthly Salary</td><td class="r">₱{{ number_format($monthlySalary, 2) }}</td></tr>
                            <tr class="even"><td>Semi-Monthly Basic (÷ 2)</td><td class="r"><strong>₱{{ number_format($payroll->basic_salary, 2) }}</strong></td></tr>
                            <tr><td>Daily Rate (÷ 22 days)</td><td class="r">₱{{ number_format($dailyRate, 2) }}</td></tr>
                            <tr class="even"><td>Hourly Rate</td><td class="r">₱{{ number_format($hourlyRate, 2) }}</td></tr>
                        </table>
                    </div>
                    <div>
                        <div class="ps-sec-header">Attendance Summary</div>
                        <table class="ps-table">
                            <tr><td>Days Worked</td><td class="r"><strong>{{ $payroll->days_worked }}</strong></td></tr>
                            <tr class="even"><td>Absent Days</td><td class="r text-red">{{ $payroll->absent_days }}</td></tr>
                            <tr><td>Late (minutes)</td><td class="r text-red">{{ $payroll->late_minutes }}</td></tr>
                            <tr class="even"><td>Undertime (minutes)</td><td class="r text-red">{{ $payroll->undertime_minutes }}</td></tr>
                            <tr><td>Overtime (hours)</td><td class="r text-green">{{ number_format($otHours, 2) }}</td></tr>
                        </table>
                    </div>
                </div>

                {{-- Two-column: Earnings + Attendance Deductions --}}
                <div class="ps-two-col">
                    <div>
                        <div class="ps-sec-header">Earnings</div>
                        <table class="ps-table">
                            <thead>
                                <tr><th>Description</th><th class="r">Amount</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Basic Salary (Semi-Monthly)</td><td class="r">₱{{ number_format($payroll->basic_salary, 2) }}</td></tr>
                                <tr class="even"><td>Overtime Pay</td><td class="r text-green">₱{{ number_format($payroll->overtime_pay, 2) }}</td></tr>
                                <tr class="ps-total"><td>Gross Pay</td><td class="r text-blue">₱{{ number_format($payroll->gross_pay, 2) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <div class="ps-sec-header">Attendance Deductions</div>
                        <table class="ps-table">
                            <thead>
                                <tr><th>Description</th><th class="r">Amount</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Absence Deduction</td><td class="r text-red">₱{{ number_format($absenceDeduct, 2) }}</td></tr>
                                <tr class="even"><td>Late Deduction</td><td class="r text-red">₱{{ number_format($lateDeduct, 2) }}</td></tr>
                                <tr><td>Undertime Deduction</td><td class="r text-red">₱{{ number_format($underDeduct, 2) }}</td></tr>
                                <tr class="ps-total"><td>Total Attendance Deductions</td><td class="r text-red">₱{{ number_format($attTotal, 2) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Government Contributions (full width) --}}
                <div class="ps-sec-header">Government Contributions &amp; Mandatory Deductions</div>
                <table class="ps-table">
                    <thead>
                        <tr>
                            <th>Contribution</th>
                            <th class="r">Employee Share</th>
                            <th class="r">Employer Share</th>
                            <th class="r">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>SSS</td>
                            <td class="r">₱{{ number_format($breakdown['sss']['employee_share'] ?? 0, 2) }}</td>
                            <td class="r">₱{{ number_format($breakdown['sss']['employer_share'] ?? 0, 2) }}</td>
                            <td class="r">₱{{ number_format($breakdown['sss']['total'] ?? 0, 2) }}</td>
                        </tr>
                        <tr class="even">
                            <td>PhilHealth</td>
                            <td class="r">₱{{ number_format($breakdown['philhealth']['employee_share'] ?? 0, 2) }}</td>
                            <td class="r">₱{{ number_format($breakdown['philhealth']['employer_share'] ?? 0, 2) }}</td>
                            <td class="r">₱{{ number_format($breakdown['philhealth']['total'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Pag-IBIG (HDMF)</td>
                            <td class="r">₱{{ number_format($breakdown['pagibig']['employee_share'] ?? 0, 2) }}</td>
                            <td class="r">₱{{ number_format($breakdown['pagibig']['employer_share'] ?? 0, 2) }}</td>
                            <td class="r">₱{{ number_format($breakdown['pagibig']['total'] ?? 0, 2) }}</td>
                        </tr>
                        <tr class="even">
                            <td>Withholding Tax (BIR)</td>
                            <td class="r">₱{{ number_format($breakdown['tax']['employee_share'] ?? 0, 2) }}</td>
                            <td class="r">&mdash;</td>
                            <td class="r">₱{{ number_format($breakdown['tax']['employee_share'] ?? 0, 2) }}</td>
                        </tr>
                        <tr class="ps-total">
                            <td>Total Government Deductions</td>
                            <td class="r text-red">₱{{ number_format(
                                ($breakdown['sss']['employee_share'] ?? 0) +
                                ($breakdown['philhealth']['employee_share'] ?? 0) +
                                ($breakdown['pagibig']['employee_share'] ?? 0) +
                                ($breakdown['tax']['employee_share'] ?? 0), 2) }}</td>
                            <td class="r"></td>
                            <td class="r text-red">₱{{ number_format(
                                ($breakdown['sss']['total'] ?? 0) +
                                ($breakdown['philhealth']['total'] ?? 0) +
                                ($breakdown['pagibig']['total'] ?? 0) +
                                ($breakdown['tax']['employee_share'] ?? 0), 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Net Pay Banner --}}
                <div class="ps-net-banner">
                    <div class="ps-net-label">Total Net Pay for This Period</div>
                    <div class="ps-net-amount">₱{{ number_format($payroll->net_pay, 2) }}</div>
                    <div class="ps-net-sub">
                        {{ \Carbon\Carbon::parse($payroll->payroll_period_start)->format('M d') }}
                        &ndash; {{ \Carbon\Carbon::parse($payroll->payroll_period_end)->format('M d, Y') }}
                    </div>
                </div>

                {{-- Final Computation + Signatures side-by-side --}}
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start;">
                    {{-- Final Computation --}}
                    <div>
                        <div class="ps-sec-header" style="border-radius:0.35rem 0.35rem 0 0;">Final Computation</div>
                        <div style="border:1px solid rgba(255,255,255,0.08); border-top:none; border-radius:0 0 0.5rem 0.5rem; padding:1rem 1.25rem;">
                            <div class="ps-comp-row">
                                <span>Gross Pay</span>
                                <strong>₱{{ number_format($payroll->gross_pay, 2) }}</strong>
                            </div>
                            <div class="ps-comp-row cr-deduct">
                                <span>( &minus; ) Total Deductions</span>
                                <strong>₱{{ number_format($payroll->total_deductions, 2) }}</strong>
                            </div>
                            <hr class="ps-comp-divider">
                            <div class="ps-comp-total">
                                <span style="color:var(--text-main,#f8fafc); font-weight:800;">Net Pay</span>
                                <span>₱{{ number_format($payroll->net_pay, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Signatures --}}
                    <div class="ps-sig-grid" style="display:block; border:none; padding:0; margin:0;">
                        <div style="margin-bottom:1.5rem;">
                            <div class="ps-sig-label">Prepared By:</div>
                            <div class="ps-sig-line">
                                <div class="ps-sig-name">HR / Payroll Officer</div>
                                <div class="ps-sig-role">Authorized Signature over Printed Name</div>
                            </div>
                        </div>
                        <div>
                            <div class="ps-sig-label">Received by (Employee Signature):</div>
                            <div class="ps-sig-line">
                                <div class="ps-sig-name">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                <div class="ps-sig-role">Signature over Printed Name &amp; Date</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- end ps-body --}}
        </div>{{-- end ps-card --}}
    @endif
</div>
@endsection