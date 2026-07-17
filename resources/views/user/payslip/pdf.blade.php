<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payslip &ndash; {{ $employee->name }}</title>
    <style>
        /* ─── Reset ─────────────────────────────────────── */
        * { margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #1e293b;
            background: #fff;
        }

        /* ─── Outer wrapper (gives us 25pt margin on all sides safely) ─── */
        .outer {
            width: 535pt; /* A4 595pt – 30pt each side */
            margin: 0 auto;
        }

        /* ─── Header ─────────────────────────────────── */
        .hdr-table { width: 535pt; border-collapse: collapse; }
        .company-name  { font-size: 15pt; font-weight: bold; color: #1d4ed8; }
        .company-sub   { font-size: 7.5pt; color: #64748b; margin-top: 2pt; }
        .ps-title      { font-size: 22pt; font-weight: bold; color: #0f172a; letter-spacing: 2pt; text-transform: uppercase; text-align: right; }
        .ps-period     { font-size: 7.5pt; color: #475569; text-align: right; margin-top: 2pt; }
        .hdr-rule      { width: 535pt; height: 0; border: 0; border-top: 3px solid #1d4ed8; margin: 8pt 0; }

        /* ─── Employee Info Band ─────────────────────── */
        .emp-band {
            width: 535pt;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 8pt 10pt;
            margin-bottom: 10pt;
        }
        .emp-band-table { width: 100%; border-collapse: collapse; }
        .emp-band-table td { width: 25%; padding: 0 4pt; vertical-align: top; }
        .elabel { font-size: 7pt; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.3pt; }
        .evalue { font-size: 9pt; font-weight: bold; color: #0f172a; margin-top: 1pt; }

        /* ─── Section header ─────────────────────────── */
        .sec-hdr {
            background: #1d4ed8;
            color: #fff;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6pt;
            padding: 4pt 7pt;
        }

        /* ─── Data table (full width) ────────────────── */
        .dt { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 10pt; }
        .dt th { background: #e2e8f0; color: #334155; font-size: 7pt; font-weight: bold; text-transform: uppercase; padding: 4pt 7pt; text-align: left; border: 1px solid #cbd5e1; }
        .dt th.r, .dt td.r { text-align: right; }
        .dt td { padding: 4pt 7pt; border: 1px solid #e2e8f0; }
        .dt tr.even td { background: #f8fafc; }
        .dt tr.tot td { font-weight: bold; background: #f1f5f9; border-top: 1.5px solid #94a3b8; }

        /* ─── Two-column container ───────────────────── */
        .tc { width: 535pt; border-collapse: collapse; margin-bottom: 10pt; }
        .tc-l { width: 260pt; vertical-align: top; padding-right: 7pt; }
        .tc-r { width: 268pt; vertical-align: top; }

        /* inner tables inside two-col must be full-width of their cell */
        .tc-l .dt, .tc-r .dt { width: 100%; }

        /* ─── Computation box ────────────────────────── */
        .comp-box { border: 1.5px solid #e2e8f0; padding: 8pt 12pt; margin-bottom: 10pt; width: 535pt; }
        .comp-table { width: 100%; border-collapse: collapse; }
        .comp-table td { padding: 3pt 0; font-size: 9pt; border: 0; }
        .cl { color: #475569; }
        .cv { text-align: right; font-weight: bold; color: #0f172a; }
        .cr-red  { color: #ef4444; }
        .cr-blue { color: #1d4ed8; }
        .comp-line td { border-top: 2px solid #1d4ed8; padding-top: 5pt; font-size: 10.5pt; font-weight: bold; }
        .comp-line td.cv { color: #1d4ed8; }

        /* ─── Net pay banner ─────────────────────────── */
        .net-banner {
            width: 535pt;
            background: #1d4ed8;
            color: #fff;
            text-align: center;
            padding: 12pt 0;
            margin-bottom: 12pt;
        }
        .net-label  { font-size: 8pt; text-transform: uppercase; letter-spacing: 1.5pt; opacity: 0.85; }
        .net-amount { font-size: 24pt; font-weight: bold; }
        .net-note   { font-size: 7pt; opacity: 0.75; margin-top: 2pt; }

        /* ─── Signature section ───────────────────────── */
        .sig-table { width: 535pt; border-collapse: collapse; margin-top: 18pt; }
        .sig-table td { width: 50%; padding: 0 15pt; vertical-align: bottom; }
        .sig-table td:first-child { padding-left: 0; }
        .sig-table td:last-child  { padding-right: 0; }
        .sig-spacer { height: 30pt; }
        .sig-line { border-top: 1px solid #1e293b; padding-top: 4pt; font-size: 8pt; color: #475569; }
        .sig-name  { font-size: 9pt; font-weight: bold; color: #0f172a; margin-top: 2pt; }

        /* ─── Footer ─────────────────────────────────── */
        .footer { width: 535pt; border-top: 1px solid #e2e8f0; margin-top: 14pt; padding-top: 6pt; text-align: center; font-size: 6.5pt; color: #94a3b8; }

        /* ─── Utilities ──────────────────────────────── */
        .red   { color: #ef4444; }
        .green { color: #10b981; }
        .blue  { color: #1d4ed8; }
        .bold  { font-weight: bold; }
        .mb6   { margin-bottom: 6pt; }
    </style>
</head>
<body>
<table style="width:100%; border-collapse:collapse;">
    <tr><td style="padding: 25pt 30pt 28pt;">

    {{-- ══ HEADER ══ --}}
    <table class="hdr-table">
        <tr>
            <td style="vertical-align:top;">
                <div class="company-name">VIA Architects Associates</div>
                <div class="company-sub">Human Resources &amp; Payroll Department</div>
            </td>
            <td style="text-align:right; vertical-align:top;">
                <div class="ps-title">Payslip</div>
                <div class="ps-period">
                    Period: {{ \Carbon\Carbon::parse($payroll->payroll_period_start)->format('M d, Y') }}
                    &ndash; {{ \Carbon\Carbon::parse($payroll->payroll_period_end)->format('M d, Y') }}
                </div>
                <div class="ps-period">Pay Date: {{ \Carbon\Carbon::parse($payroll->payroll_date)->format('M d, Y') }}</div>
            </td>
        </tr>
    </table>
    <div class="hdr-rule"></div>

    {{-- ══ EMPLOYEE INFO ══ --}}
    <table class="emp-band-table mb6" style="background:#f1f5f9; border:1px solid #cbd5e1; padding:8pt; margin-bottom:10pt; width:535pt;">
        <tr>
            <td style="width:25%; padding:0 4pt; vertical-align:top;">
                <div class="elabel">Employee Name</div>
                <div class="evalue">{{ $employee->first_name }} {{ $employee->last_name }}</div>
            </td>
            <td style="width:25%; padding:0 4pt; vertical-align:top;">
                <div class="elabel">Employee ID</div>
                <div class="evalue">{{ $employee->employee_number ?? ('VIA-' . date('Y') . '-' . str_pad($employee->employee_id, 3, '0', STR_PAD_LEFT)) }}</div>
            </td>
            <td style="width:25%; padding:0 4pt; vertical-align:top;">
                <div class="elabel">Position</div>
                <div class="evalue">{{ $employee->position->position_name ?? 'N/A' }}</div>
            </td>
            <td style="width:25%; padding:0 4pt; vertical-align:top;">
                <div class="elabel">Department</div>
                <div class="evalue">{{ $employee->department->department_name ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    {{-- ══ TWO-COL: Salary Summary + Attendance ══ --}}
    <table class="tc">
        <tr>
            <td class="tc-l">
                <div class="sec-hdr">Salary Summary</div>
                <table class="dt">
                    <tr><td>Monthly Salary</td><td class="r">&#8369;{{ number_format($monthlySalary, 2) }}</td></tr>
                    <tr class="even"><td>Semi-Monthly Basic (&divide; 2)</td><td class="r bold">&#8369;{{ number_format($payroll->basic_salary, 2) }}</td></tr>
                    <tr><td>Daily Rate (&divide; 22 days)</td><td class="r">&#8369;{{ number_format($monthlySalary > 0 ? $monthlySalary/22 : 0, 2) }}</td></tr>
                    <tr class="even"><td>Hourly Rate</td><td class="r">&#8369;{{ number_format($monthlySalary > 0 ? $monthlySalary/22/8 : 0, 2) }}</td></tr>
                </table>
            </td>
            <td class="tc-r">
                <div class="sec-hdr">Attendance Summary</div>
                <table class="dt">
                    <tr><td>Days Worked</td><td class="r bold">{{ $payroll->days_worked }}</td></tr>
                    <tr class="even"><td>Absent Days</td><td class="r red">{{ $payroll->absent_days }}</td></tr>
                    <tr><td>Late (minutes)</td><td class="r red">{{ $payroll->late_minutes }}</td></tr>
                    <tr class="even"><td>Undertime (minutes)</td><td class="r red">{{ $payroll->undertime_minutes }}</td></tr>
                    <tr><td>Overtime (hours)</td><td class="r green">
                        {{ number_format($payroll->overtime_pay > 0 && $monthlySalary > 0
                            ? $payroll->overtime_pay / (($monthlySalary / 22 / 8) * 1.25) : 0, 2) }}
                    </td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ══ TWO-COL: Earnings + Attendance Deductions ══ --}}
    <table class="tc">
        <tr>
            <td class="tc-l">
                <div class="sec-hdr">Earnings</div>
                <table class="dt">
                    <thead>
                        <tr><th>Description</th><th class="r">Amount</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Basic Salary (Semi-Monthly)</td><td class="r">&#8369;{{ number_format($payroll->basic_salary, 2) }}</td></tr>
                        <tr class="even"><td>Overtime Pay</td><td class="r">&#8369;{{ number_format($payroll->overtime_pay, 2) }}</td></tr>
                        <tr class="tot"><td>Gross Pay</td><td class="r blue">&#8369;{{ number_format($payroll->gross_pay, 2) }}</td></tr>
                    </tbody>
                </table>
            </td>
            <td class="tc-r">
                <div class="sec-hdr">Attendance Deductions</div>
                <table class="dt">
                    <thead>
                        <tr><th>Description</th><th class="r">Amount</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Absence Deduction</td><td class="r red">&#8369;{{ number_format($absenceDeduction, 2) }}</td></tr>
                        <tr class="even"><td>Late Deduction</td><td class="r red">&#8369;{{ number_format($lateDeduction, 2) }}</td></tr>
                        <tr><td>Undertime Deduction</td><td class="r red">&#8369;{{ number_format($undertimeDeduction, 2) }}</td></tr>
                        <tr class="tot"><td>Total Attendance Deductions</td><td class="r red">&#8369;{{ number_format($attendanceTotal, 2) }}</td></tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    {{-- ══ GOVERNMENT CONTRIBUTIONS ══ --}}
    <div class="sec-hdr" style="width:535pt; margin-bottom:0;">Government Contributions &amp; Mandatory Deductions</div>
    <table class="dt" style="width:535pt; margin-bottom:10pt;">
        <thead>
            <tr>
                <th style="width:35%;">Contribution</th>
                <th class="r" style="width:21%;">Employee Share</th>
                <th class="r" style="width:21%;">Employer Share</th>
                <th class="r" style="width:23%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>SSS</td>
                <td class="r">&#8369;{{ number_format($breakdown['sss']['employee_share'] ?? 0, 2) }}</td>
                <td class="r">&#8369;{{ number_format($breakdown['sss']['employer_share'] ?? 0, 2) }}</td>
                <td class="r">&#8369;{{ number_format($breakdown['sss']['total'] ?? 0, 2) }}</td>
            </tr>
            <tr class="even">
                <td>PhilHealth</td>
                <td class="r">&#8369;{{ number_format($breakdown['philhealth']['employee_share'] ?? 0, 2) }}</td>
                <td class="r">&#8369;{{ number_format($breakdown['philhealth']['employer_share'] ?? 0, 2) }}</td>
                <td class="r">&#8369;{{ number_format($breakdown['philhealth']['total'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Pag-IBIG (HDMF)</td>
                <td class="r">&#8369;{{ number_format($breakdown['pagibig']['employee_share'] ?? 0, 2) }}</td>
                <td class="r">&#8369;{{ number_format($breakdown['pagibig']['employer_share'] ?? 0, 2) }}</td>
                <td class="r">&#8369;{{ number_format($breakdown['pagibig']['total'] ?? 0, 2) }}</td>
            </tr>
            <tr class="even">
                <td>Withholding Tax (BIR)</td>
                <td class="r">&#8369;{{ number_format($breakdown['tax']['employee_share'] ?? 0, 2) }}</td>
                <td class="r">&mdash;</td>
                <td class="r">&#8369;{{ number_format($breakdown['tax']['employee_share'] ?? 0, 2) }}</td>
            </tr>
            <tr class="tot">
                <td>Total Government Deductions</td>
                <td class="r red">&#8369;{{ number_format(
                    ($breakdown['sss']['employee_share'] ?? 0) +
                    ($breakdown['philhealth']['employee_share'] ?? 0) +
                    ($breakdown['pagibig']['employee_share'] ?? 0) +
                    ($breakdown['tax']['employee_share'] ?? 0), 2) }}</td>
                <td class="r"></td>
                <td class="r red">&#8369;{{ number_format(
                    ($breakdown['sss']['total'] ?? 0) +
                    ($breakdown['philhealth']['total'] ?? 0) +
                    ($breakdown['pagibig']['total'] ?? 0) +
                    ($breakdown['tax']['employee_share'] ?? 0), 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ══ FINAL COMPUTATION ══ --}}
    <div class="sec-hdr" style="width:535pt;">Final Computation</div>
    <div style="border:1.5px solid #e2e8f0; padding:8pt 12pt; margin-bottom:10pt; width:511pt;">
        <table class="comp-table">
            <tr>
                <td class="cl">Gross Pay</td>
                <td class="cv">&#8369;{{ number_format($payroll->gross_pay, 2) }}</td>
            </tr>
            <tr>
                <td class="cl cr-red">( &minus; ) Total Deductions</td>
                <td class="cv cr-red">&#8369;{{ number_format($payroll->total_deductions, 2) }}</td>
            </tr>
            <tr class="comp-line">
                <td class="cl bold" style="font-size:10.5pt;">Net Pay</td>
                <td class="cv cr-blue" style="font-size:10.5pt;">&#8369;{{ number_format($payroll->net_pay, 2) }}</td>
            </tr>
        </table>
    </div>

    {{-- ══ NET PAY BANNER ══ --}}
    <table style="width:535pt; background:#1d4ed8; border-collapse:collapse; margin-bottom:12pt;">
        <tr>
            <td style="text-align:center; padding:12pt 0; color:#fff;">
                <div class="net-label">Total Net Pay for This Period</div>
                <div class="net-amount">&#8369;{{ number_format($payroll->net_pay, 2) }}</div>
                <div class="net-note">
                    {{ \Carbon\Carbon::parse($payroll->payroll_period_start)->format('M d') }}
                    &ndash; {{ \Carbon\Carbon::parse($payroll->payroll_period_end)->format('M d, Y') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ══ SIGNATURES ══ --}}
    <table class="sig-table">
        <tr>
            <td style="padding:0 15pt 0 0; vertical-align:bottom; width:50%;">
                <div style="font-size:8pt; color:#475569; margin-bottom:2pt;">Prepared By:</div>
                <div class="sig-spacer"></div>
                <div style="border-top:1px solid #1e293b; padding-top:4pt;">
                    <div class="sig-name">HR / Payroll Officer</div>
                    <div style="font-size:7.5pt; color:#64748b;">Authorized Signature over Printed Name</div>
                </div>
            </td>
            <td style="padding:0 0 0 15pt; vertical-align:bottom; width:50%;">
                <div style="font-size:8pt; color:#475569; margin-bottom:2pt;">Received by (Employee Signature):</div>
                <div class="sig-spacer"></div>
                <div style="border-top:1px solid #1e293b; padding-top:4pt;">
                    <div class="sig-name">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                    <div style="font-size:7.5pt; color:#64748b;">Signature over Printed Name &amp; Date</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ══ FOOTER ══ --}}
    <div class="footer">
        This payslip is a computer-generated document. &mdash;
        Generated on {{ now()->format('F d, Y \a\t h:i A') }} &mdash;
        VIA Architects Associates Payroll System
    </div>

    </td></tr>
</table>
</body>
</html>
