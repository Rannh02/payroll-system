@extends('layouts.master')

@section('title', 'Employee Payslip')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/user/style.css') }}">
    <style>
        .payslip-container {
            max-width: 800px;
            margin: 2rem auto;
            background: var(--bg-surface, #1e293b);
            border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.05));
            border-radius: 1.25rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 2.5rem;
            color: var(--text-main, #f8fafc);
        }
        .payslip-header {
            text-align: center;
            margin-bottom: 2.5rem;
            border-bottom: 1px solid var(--glass-border, rgba(255, 255, 255, 0.05));
            padding-bottom: 1.5rem;
        }
        .payslip-header h1 {
            font-size: 1.875rem;
            font-weight: 800;
            color: #3b82f6;
            margin-bottom: 0.5rem;
        }
        .payslip-header p {
            color: #94a3b8;
            font-size: 0.95rem;
            font-weight: 500;
        }
        .section-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #3b82f6;
            margin: 2rem 0 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .employee-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.02);
            padding: 1.5rem;
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.03);
        }
        .info-card .info-label {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .info-card .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-main, #f8fafc);
        }
        .payroll-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        .payroll-table th, .payroll-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--glass-border, rgba(255, 255, 255, 0.05));
        }
        .payroll-table th {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
        }
        .payroll-table td {
            font-size: 0.9rem;
            font-weight: 500;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 2.5rem;
        }
        .summary-card {
            padding: 1.5rem;
            border-radius: 1rem;
            text-align: center;
        }
        .gross-card {
            background: rgba(59, 130, 246, 0.08);
            border: 1px solid rgba(59, 130, 246, 0.15);
        }
        .deduction-card {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.15);
        }
        .net-card {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.15);
        }
        .summary-card .summary-label {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .summary-card .summary-value {
            font-size: 1.5rem;
            font-weight: 800;
        }
        .gross-card .summary-value { color: #3b82f6; }
        .deduction-card .summary-value { color: #ef4444; }
        .net-card .summary-value { color: #10b981; }
        
        .actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 3rem;
            border-top: 1px solid var(--glass-border, rgba(255, 255, 255, 0.05));
            padding-top: 1.5rem;
        }
        .btn {
            padding: 0.6rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .btn-primary {
            background: #3b82f6;
            color: white;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .btn-primary:hover { background: #2563eb; }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.1); }
    </style>
@endsection

@section('content')
<div class="payslip-container">

    @if(isset($error))
        <div style="text-align: center; padding: 2rem 0;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 50%; background: rgba(239, 68, 68, 0.1); color: #ef4444; margin-bottom: 1.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main, #f8fafc); margin-bottom: 0.5rem;">No Payslip Available</h2>
            <p style="color: #94a3b8; max-width: 450px; margin: 0 auto 2rem; font-size: 0.95rem; line-height: 1.5;">{{ $error }}</p>
            <a href="{{ Auth::user()->role === 'admin' ? route('payroll.index') : route('user.dashboard') }}" class="btn btn-primary">
                Back to Dashboard
            </a>
        </div>
    @else
        {{-- Header --}}
        <div class="payslip-header">
            <h1>Employee Payslip</h1>
            <p>
                Payroll Period: 
                {{ \Carbon\Carbon::parse($payroll->from_date)->format('F d, Y') }} 
                - 
                {{ \Carbon\Carbon::parse($payroll->to_date)->format('F d, Y') }}
            </p>
        </div>

        <div class="payslip-body">
            {{-- Employee Information --}}
            <div class="employee-grid">
                <div class="info-card">
                    <div class="info-label">Employee Name</div>
                    <div class="info-value">
                        {{ $employee->first_name }} {{ $employee->last_name }}
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-label">Department</div>
                    <div class="info-value">
                        {{ $employee->department->department_name ?? 'N/A' }}
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-label">Position</div>
                    <div class="info-value">
                        {{ $employee->position->position_name ?? 'N/A' }}
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-label">Daily Rate</div>
                    <div class="info-value">
                        ₱{{ number_format($employee->daily_rate, 2) }}
                    </div>
                </div>
            </div>

            {{-- Attendance Summary --}}
            <h2 class="section-title">Attendance Summary</h2>
            <table class="payroll-table">
                <thead>
                    <tr>
                        <th>Days Worked</th>
                        <th>Absent Days</th>
                        <th>Late Minutes</th>
                        <th>Undertime Minutes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $payroll->days_worked }}</td>
                        <td>{{ $payroll->absent_days }}</td>
                        <td>{{ $payroll->late_minutes }}</td>
                        <td>{{ $payroll->undertime_minutes }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- Government Contributions --}}
            <h2 class="section-title">Government Contributions & Deductions</h2>
            <table class="payroll-table">
                <thead>
                    <tr>
                        <th>Contribution / Deduction</th>
                        <th>Employee Share</th>
                        <th>Employer Share</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>SSS</td>
                        <td>₱{{ number_format($breakdown['sss']['employee_share'] ?? 0, 2) }}</td>
                        <td>₱{{ number_format($breakdown['sss']['employer_share'] ?? 0, 2) }}</td>
                        <td>₱{{ number_format($breakdown['sss']['total'] ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>PhilHealth</td>
                        <td>₱{{ number_format($breakdown['philhealth']['employee_share'] ?? 0, 2) }}</td>
                        <td>₱{{ number_format($breakdown['philhealth']['employer_share'] ?? 0, 2) }}</td>
                        <td>₱{{ number_format($breakdown['philhealth']['total'] ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Pag-IBIG</td>
                        <td>₱{{ number_format($breakdown['pagibig']['employee_share'] ?? 0, 2) }}</td>
                        <td>₱{{ number_format($breakdown['pagibig']['employer_share'] ?? 0, 2) }}</td>
                        <td>₱{{ number_format($breakdown['pagibig']['total'] ?? 0, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- Salary Summary --}}
            <div class="summary-grid">
                <div class="summary-card gross-card">
                    <div class="summary-label">Gross Salary</div>
                    <div class="summary-value">
                        ₱{{ number_format($payroll->gross_salary, 2) }}
                    </div>
                </div>

                <div class="summary-card deduction-card">
                    <div class="summary-label">Total Deductions</div>
                    <div class="summary-value">
                        ₱{{ number_format($payroll->total_deductions, 2) }}
                    </div>
                </div>

                <div class="summary-card net-card">
                    <div class="summary-label">Net Pay</div>
                    <div class="summary-value">
                        ₱{{ number_format($payroll->net_pay, 2) }}
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="actions">
                <button onclick="window.print()" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.5rem;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Print Payslip
                </button>

                <a href="{{ Auth::user()->role === 'admin' ? route('payroll.index') : route('user.dashboard') }}" class="btn btn-secondary">
                    Back
                </a>
            </div>
        </div>
    @endif

</div>
@endsection