@extends('layouts.master')

@section('title', 'Employee Portal - VIA Architects Associates')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/user/style.css') }}">
    <style>
        /* Clock & Calendar Premium Styles */
        .clock-widget {
            background: var(--bg-surface);
            border: 1px solid var(--glass-border);
            border-radius: 1rem;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        }

        .clock-display {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .clock-time {
            font-size: 1.35rem;
            font-weight: 800;
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            color: var(--text-main);
            letter-spacing: 0.05em;
            line-height: 1.1;
        }

        .clock-date {
            font-size: 0.6875rem;
            color: var(--slate-500);
            font-weight: 600;
            margin-top: 0.25rem;
        }

        .clock-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .btn-clock {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.5rem 1rem;
            font-size: 0.8125rem;
            font-weight: 700;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
        }

        .btn-clock-in {
            background: #10b981;
            color: white !important;
        }

        .btn-clock-in:hover:not(:disabled) {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .btn-clock-out {
            background: #ef4444;
            color: white !important;
        }

        .btn-clock-out:hover:not(:disabled) {
            background: #dc2626;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        .btn-clock:disabled {
            background: var(--bg-dark);
            color: var(--slate-500) !important;
            border: 1px solid var(--glass-border);
            cursor: not-allowed;
            opacity: 0.6;
        }

        .status-badge-live {
            font-size: 0.6875rem;
            font-weight: 700;
            padding: 0.1875rem 0.625rem;
            border-radius: 9999px;
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        /* Calendar Widget Styles */
        .calendar-widget-card {
            background: var(--bg-surface);
            border: 1px solid var(--glass-border);
            border-radius: 1.25rem;
            padding: var(--card-padding);
            margin-top: 2.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .calendar-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .date-selector-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--bg-dark);
            border: 1px solid var(--glass-border);
            padding: 0.5rem 1rem;
            border-radius: 1rem;
        }

        .date-selector-input {
            border: none;
            background: transparent;
            color: var(--text-main);
            font-weight: 700;
            font-size: 0.875rem;
            outline: none;
            cursor: pointer;
        }

        .dark-mode .date-selector-input {
            color-scheme: dark;
        }

        .calendar-status-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .calendar-status-grid {
                grid-template-columns: 1fr 2fr;
            }
        }

        .status-summary-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background: var(--bg-dark);
            border-radius: 1.25rem;
            border: 1px solid var(--glass-border);
            text-align: center;
            min-height: 220px;
        }

        .status-main-badge {
            font-size: 1.125rem;
            font-weight: 800;
            padding: 0.5rem 1.5rem;
            border-radius: 9999px;
            margin-top: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-present {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .status-absent {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .status-leave {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .status-notfiled {
            background: var(--bg-surface);
            color: var(--slate-500);
            border: 1px solid var(--glass-border);
        }

        .metrics-detail-panel {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .metric-detail-card {
            background: var(--bg-dark);
            border: 1px solid var(--glass-border);
            border-radius: 1rem;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .metric-detail-label {
            font-size: 0.6875rem;
            font-weight: 700;
            color: var(--slate-500);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .metric-detail-value {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-main);
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
@endsection

@section('content')
    <div class="main-content-inner">
        <div class="content-header">
            <div>
                <h2 class="header-title">Employee Portal</h2>
                <p class="header-subtitle">
                    <span class="subtitle-dot"></span>
                    Welcome back, {{ Auth::user()->name ?? 'User' }} — {{ date('F d, Y') }}
                </p>
            </div>
            <div class="header-actions">
                <div class="clock-widget">
                    <div class="clock-display">
                        <div class="clock-time" id="live-time">{{ date('h:i:s A') }}</div>
                        <div class="clock-date" id="live-date">{{ date('l, F d, Y') }}</div>
                    </div>
                    <div class="clock-actions">
                        <button id="btn-clock-in" class="btn-clock btn-clock-in" 
                                {{ ($todayAttendance && $todayAttendance->time_in) ? 'disabled' : '' }}>
                            <i data-lucide="log-in" class="h-4 w-4"></i>
                            <span>
                                @if($todayAttendance && $todayAttendance->time_in)
                                    In: {{ \Carbon\Carbon::parse($todayAttendance->time_in)->format('h:i A') }}
                                @else
                                    Clock In
                                @endif
                            </span>
                        </button>
                        <button id="btn-clock-out" class="btn-clock btn-clock-out"
                                {{ (!$todayAttendance || !$todayAttendance->time_in || $todayAttendance->time_out) ? 'disabled' : '' }}>
                            <i data-lucide="log-out" class="h-4 w-4"></i>
                            <span>
                                @if($todayAttendance && $todayAttendance->time_out)
                                    Out: {{ \Carbon\Carbon::parse($todayAttendance->time_out)->format('h:i A') }}
                                @elseif($todayAttendance && $todayAttendance->time_in)
                                    Clock Out
                                @else
                                    Clock Out
                                @endif
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="user-check" class="h-6 w-6"></i>
                    </div>
                    <span class="stat-badge badge-teal">Ontime</span>
                </div>
                <h3 class="stat-label">Attendance</h3>
                <p class="stat-value">{{ $stats['attendance'] }}</p>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-rose">
                        <i data-lucide="user-x" class="h-6 w-6"></i>
                    </div>
                    <span class="stat-badge badge-rose">Monthly</span>
                </div>
                <h3 class="stat-label">Absences</h3>
                <p class="stat-value">{{ $stats['absences'] }}</p>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-amber">
                        <i data-lucide="clock" class="h-6 w-6"></i>
                    </div>
                    <span class="stat-badge badge-amber">Cumulative</span>
                </div>
                <h3 class="stat-label">Late Arrivals</h3>
                <p class="stat-value">{{ $stats['late'] }}</p>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-amber">
                        <i data-lucide="clock" class="h-6 w-6"></i>
                    </div>
                    <span class="stat-badge badge-amber">Hour</span>
                </div>
                <h3 class="stat-label">Over Time</h3>
                <p class="stat-value">{{ $stats['overtime'] }}</p>
            </div>
        </div>

        <!-- Financial Summary Chart -->
        <div class="chart-card" style="margin-top: 0;">
            <div class="chart-card-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 class="chart-card-title">
                        <i data-lucide="trending-up" class="h-5 w-5" style="color: #3b82f6;"></i>
                        Financial Summary — Payroll by Month
                    </h3>
                    <p class="chart-card-subtitle">Monthly payroll expenses for the current year</p>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <div class="summary-item" style="display: flex; align-items: center; gap: 0.75rem; background: var(--bg-surface); border: 1px solid var(--glass-border); padding: 0.75rem 1.25rem; border-radius: 1rem; margin: 0;">
                        <span class="item-label" style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Latest Payroll</span>
                        <span class="item-value" style="font-size: 1.125rem; font-weight: 800; color: var(--text-main);">₱{{ number_format($latestPayrollAmount, 2) }}</span>
                    </div>
                    <div class="summary-item" style="display: flex; align-items: center; gap: 0.75rem; background: var(--bg-surface); border: 1px solid var(--glass-border); padding: 0.75rem 1.25rem; border-radius: 1rem; margin: 0;">
                        <span class="item-label" style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Year to Date (YTD)</span>
                        <span class="item-value" style="font-size: 1.125rem; font-weight: 800; color: var(--text-main);">₱{{ number_format($ytdPayroll, 2) }}</span>
                    </div>
                    <button id="btn-view-payroll" class="btn-view-payroll" style="align-self: center;">
                        <i data-lucide="external-link" class="mr-2 h-4 w-4"></i> View Details
                    </button>
                </div>
            </div>
            <div class="chart-container" style="height: 280px;">
                <canvas id="financialSummaryChart"></canvas>
            </div>
        </div>

        <!-- Attendance Date Selector & Status Widget -->
        <div class="calendar-widget-card">
            <div class="calendar-header">
                <h3 class="calendar-title">
                    <i data-lucide="calendar-days" class="h-5 w-5" style="color: #3b82f6;"></i>
                    Daily Attendance Checker
                </h3>
                <div class="date-selector-container">
                    <i data-lucide="calendar" class="h-4 w-4 text-slate-500"></i>
                    <input type="date" id="calendar-date-selector" class="date-selector-input" value="{{ date('Y-m-d') }}">
                </div>
            </div>
            <div class="calendar-status-grid">
                <!-- Status Summary Panel -->
                <div class="status-summary-panel" id="status-panel-summary">
                    <p class="filter-label" style="font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase;">Day Status</p>
                    <div id="status-main-badge-element" class="status-main-badge status-notfiled">
                        Checking...
                    </div>
                    <p class="announcement-desc" id="status-leave-details" style="margin-top: 1rem; display: none; line-height: 1.5; font-size: 0.8125rem;"></p>
                </div>
                <!-- Metrics Detail Panel -->
                <div class="metrics-detail-panel">
                    <div class="metric-detail-card">
                        <span class="metric-detail-label">Time In</span>
                        <span class="metric-detail-value" id="metric-time-in">—</span>
                    </div>
                    <div class="metric-detail-card">
                        <span class="metric-detail-label">Time Out</span>
                        <span class="metric-detail-value" id="metric-time-out">—</span>
                    </div>
                    <div class="metric-detail-card">
                        <span class="metric-detail-label">Late Minutes</span>
                        <span class="metric-detail-value" id="metric-late">0m</span>
                    </div>
                    <div class="metric-detail-card">
                        <span class="metric-detail-label">Undertime Minutes</span>
                        <span class="metric-detail-value" id="metric-undertime">0m</span>
                    </div>
                    <div class="metric-detail-card">
                        <span class="metric-detail-label">Overtime Hours</span>
                        <span class="metric-detail-value" id="metric-overtime">0.00h</span>
                    </div>
                    <div class="metric-detail-card">
                        <span class="metric-detail-label">Total Hours Worked</span>
                        <span class="metric-detail-value" id="metric-total-hours">0.00h</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wireframe Charts -->
        <div class="charts-grid">
            <!-- Deduction Composition -->
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">
                        <i data-lucide="package" class="h-5 w-5" style="color: #f59e0b;"></i>
                        Deduction Composition — Per Employee
                    </h3>
                    <p class="chart-card-subtitle">Stacked breakdown of SSS, PhilHealth, Pag-IBIG & Tax</p>
                </div>
                <div class="chart-container">
                    <canvas id="deductionChart"></canvas>
                </div>
            </div>

            <!-- Attendance Summary -->
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">
                        <i data-lucide="calendar" class="h-5 w-5" style="color: #6366f1;"></i>
                        Attendance Summary — Per Employee
                    </h3>
                    <p class="chart-card-subtitle">Present, absent & late days for June 2025</p>
                </div>
                <div class="chart-container">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    <!-- Payroll Summary Modal -->
    <div id="payroll-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header modal-header-flex">
                <h3 class="modal-title">Payroll Summary</h3>
                <div class="modal-actions">
                    <button id="btn-close-modal" class="modal-close">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="modal-table-container">
                    <table class="modal-table">
                        <thead>
                            <tr>
                                <th>Pay Period</th>
                                <th>Basic Salary</th>
                                <th>OT Pay</th>
                                <th>Gross Pay</th>
                                <th>SSS</th>
                                <th>PhilHealth</th>
                                <th>Pag-IBIG</th>
                                <th>Tax</th>
                                <th>Total Deductions</th>
                                <th>Net Pay</th>
                                <th>Payslip</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $p)
                            <tr>
                                <td style="white-space: nowrap;">
                                    {{ \Carbon\Carbon::parse($p->payroll_period_start)->format('M d') }}
                                    –
                                    {{ \Carbon\Carbon::parse($p->payroll_period_end)->format('M d, Y') }}
                                </td>
                                <td>₱{{ number_format($p->basic_salary, 2) }}</td>
                                <td>₱{{ number_format($p->overtime_pay ?? 0, 2) }}</td>
                                <td style="font-weight: 600;">₱{{ number_format($p->gross_pay, 2) }}</td>
                                <td style="color: #ef4444;">-₱{{ number_format($p->sss ?? 0, 2) }}</td>
                                <td style="color: #ef4444;">-₱{{ number_format($p->philhealth ?? 0, 2) }}</td>
                                <td style="color: #ef4444;">-₱{{ number_format($p->hdmf ?? 0, 2) }}</td>
                                <td style="color: #ef4444;">-₱{{ number_format($p->tax ?? 0, 2) }}</td>
                                <td style="color: #ef4444; font-weight: 600;">-₱{{ number_format($p->total_deductions, 2) }}</td>
                                <td style="color: #10b981; font-weight: 700;">₱{{ number_format($p->net_pay, 2) }}</td>
                                <td>
                                    <a href="{{ route('user.payslip', ['payroll_id' => $p->payroll_id]) }}" class="btn-icon" title="View Payslip">
                                        <i data-lucide="eye" class="h-4 w-4"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" style="text-align: center; padding: 2rem; color: #64748b;">
                                    No payroll records found yet. Contact your admin to process payroll.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($payrolls->count() > 0)
                        <tfoot>
                            <tr class="table-row-total">
                                <td style="font-weight: 700;">TOTAL</td>
                                <td>₱{{ number_format($payrolls->sum('basic_salary'), 2) }}</td>
                                <td>₱{{ number_format($payrolls->sum('overtime_pay'), 2) }}</td>
                                <td style="font-weight: 600;">₱{{ number_format($payrolls->sum('gross_pay'), 2) }}</td>
                                <td style="color: #ef4444;">-₱{{ number_format($payrolls->sum(fn($p) => $p->sss), 2) }}</td>
                                <td style="color: #ef4444;">-₱{{ number_format($payrolls->sum(fn($p) => $p->philhealth), 2) }}</td>
                                <td style="color: #ef4444;">-₱{{ number_format($payrolls->sum(fn($p) => $p->hdmf), 2) }}</td>
                                <td style="color: #ef4444;">-₱{{ number_format($payrolls->sum(fn($p) => $p->tax), 2) }}</td>
                                <td style="color: #ef4444; font-weight: 600;">-₱{{ number_format($payrolls->sum('total_deductions'), 2) }}</td>
                                <td style="color: #10b981; font-weight: 700;">₱{{ number_format($payrolls->sum('net_pay'), 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Modal Logic
            const modal = document.getElementById('payroll-modal');
            const openBtn = document.getElementById('btn-view-payroll');
            const closeBtn = document.getElementById('btn-close-modal');

            if (openBtn) {
                openBtn.addEventListener('click', function () {
                    modal.classList.add('show');
                    lucide.createIcons();
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    modal.classList.remove('show');
                });
            }

            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    modal.classList.remove('show');
                }
            });

            // Dark mode variable detection for charts
            const isDark = document.documentElement.classList.contains('dark-mode');
            const textColor = isDark ? '#94a3b8' : '#64748b';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';

            // Common Chart Options
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { 
                            usePointStyle: true, 
                            boxWidth: 8,
                            color: textColor,
                            font: { family: "'Inter', sans-serif", weight: 600 }
                        }
                    }
                }
            };

            // 1. Financial Summary Line Chart
            new Chart(document.getElementById('financialSummaryChart'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Monthly Payroll (₱)',
                        data: @json($financialData),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: gridColor },
                            ticks: { color: textColor, callback: function(value) { return '₱' + (value/1000) + 'k'; } }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { color: textColor }
                        }
                    }
                }
            });

            // 2. Deduction Composition (Grouped Bar)
            new Chart(document.getElementById('deductionChart'), {
                type: 'bar',
                data: {
                    labels: ["{{ Auth::user()->name }}"],
                    datasets: [
                        { 
                            label: 'SSS', 
                            data: [{{ $deductionData['sss'] }}], 
                            backgroundColor: '#8b5cf6', 
                            barPercentage: 0.6,
                            categoryPercentage: 0.8,
                            borderRadius: 4
                        },
                        { 
                            label: 'PhilHealth', 
                            data: [{{ $deductionData['philhealth'] }}], 
                            backgroundColor: '#10b981',
                            barPercentage: 0.6,
                            categoryPercentage: 0.8,
                            borderRadius: 4
                        },
                        { 
                            label: 'Pag-IBIG', 
                            data: [{{ $deductionData['pagibig'] }}], 
                            backgroundColor: '#0ea5e9',
                            barPercentage: 0.6,
                            categoryPercentage: 0.8,
                            borderRadius: 4
                        },
                        { 
                            label: 'Tax', 
                            data: [{{ $deductionData['tax'] }}], 
                            backgroundColor: '#ef4444',
                            barPercentage: 0.6,
                            categoryPercentage: 0.8,
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        x: { 
                            grid: { display: false },
                            ticks: { color: textColor }
                        },
                        y: { 
                            grid: { borderDash: [4, 4], color: gridColor }, 
                            ticks: { 
                                color: textColor, 
                                callback: function(value) { 
                                    return '₱' + value.toLocaleString(); 
                                } 
                            } 
                        }
                    }
                }
            });

            // 3. Attendance Summary (Grouped Bar)
            new Chart(document.getElementById('attendanceChart'), {
                type: 'bar',
                data: {
                    labels: ["{{ Auth::user()->name }}"],
                    datasets: [
                        { label: 'Present Days', data: [{{ $attendanceSummary['present'] }}], backgroundColor: '#10b981', barPercentage: 0.6, categoryPercentage: 0.8, borderRadius: 2 },
                        { label: 'Absent Days', data: [{{ $attendanceSummary['absent'] }}], backgroundColor: '#ef4444', barPercentage: 0.6, categoryPercentage: 0.8, borderRadius: 2 },
                        { label: 'Late Days', data: [{{ $attendanceSummary['late'] }}], backgroundColor: '#f59e0b', barPercentage: 0.6, categoryPercentage: 0.8, borderRadius: 2 },
                        { label: 'OT Hours', data: [{{ $attendanceSummary['ot'] }}], backgroundColor: '#3b82f6', barPercentage: 0.6, categoryPercentage: 0.8, borderRadius: 2 }
                    ]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        x: { 
                            grid: { display: false },
                            ticks: { color: textColor }
                        },
                        y: { 
                            grid: { borderDash: [4, 4], color: gridColor }, 
                            min: 0, 
                            ticks: { stepSize: 5, color: textColor } 
                        }
                    }
                }
            });
            // ─────────────────────────────────────────────
            // Real-Time Dynamic Clock
            // ─────────────────────────────────────────────
            const liveTimeEl = document.getElementById('live-time');
            if (liveTimeEl) {
                setInterval(() => {
                    const now = new Date();
                    liveTimeEl.textContent = now.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: true
                    });
                }, 1000);
            }

            // ─────────────────────────────────────────────
            // Clock-In / Clock-Out Handlers
            // ─────────────────────────────────────────────
            const btnClockIn = document.getElementById('btn-clock-in');
            const btnClockOut = document.getElementById('btn-clock-out');

            if (btnClockIn) {
                btnClockIn.addEventListener('click', function () {
                    btnClockIn.disabled = true;
                    btnClockIn.innerHTML = '<i data-lucide="loader" class="h-4 w-4 animate-spin"></i><span>Clocking In...</span>';
                    if (window.lucide) window.lucide.createIcons();

                    fetch('{{ route("user.clock_in") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            btnClockIn.className = 'btn-clock btn-clock-in';
                            btnClockIn.innerHTML = `<i data-lucide="check-circle" class="h-4 w-4"></i><span>In: ${data.time_in}</span>`;
                            if (btnClockOut) {
                                btnClockOut.disabled = false;
                            }
                            if (window.lucide) window.lucide.createIcons();
                            
                            // Smooth reload to update charts and stats
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            alert(data.message || 'Error occurred during Clock-In.');
                            btnClockIn.disabled = false;
                            btnClockIn.innerHTML = '<i data-lucide="log-in" class="h-4 w-4"></i><span>Clock In</span>';
                            if (window.lucide) window.lucide.createIcons();
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Network error during Clock-In.');
                        btnClockIn.disabled = false;
                        btnClockIn.innerHTML = '<i data-lucide="log-in" class="h-4 w-4"></i><span>Clock In</span>';
                        if (window.lucide) window.lucide.createIcons();
                    });
                });
            }

            if (btnClockOut) {
                btnClockOut.addEventListener('click', function () {
                    btnClockOut.disabled = true;
                    btnClockOut.innerHTML = '<i data-lucide="loader" class="h-4 w-4 animate-spin"></i><span>Clocking Out...</span>';
                    if (window.lucide) window.lucide.createIcons();

                    fetch('{{ route("user.clock_out") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            btnClockOut.className = 'btn-clock btn-clock-out';
                            btnClockOut.innerHTML = `<i data-lucide="check-circle" class="h-4 w-4"></i><span>Out: ${data.time_out}</span>`;
                            btnClockIn.disabled = true;
                            if (window.lucide) window.lucide.createIcons();
                            
                            // Smooth reload to update charts and stats
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            alert(data.message || 'Error occurred during Clock-Out.');
                            btnClockOut.disabled = false;
                            btnClockOut.innerHTML = '<i data-lucide="log-out" class="h-4 w-4"></i><span>Clock Out</span>';
                            if (window.lucide) window.lucide.createIcons();
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Network error during Clock-Out.');
                        btnClockOut.disabled = false;
                        btnClockOut.innerHTML = '<i data-lucide="log-out" class="h-4 w-4"></i><span>Clock Out</span>';
                        if (window.lucide) window.lucide.createIcons();
                    });
                });
            }

            // ─────────────────────────────────────────────
            // Daily Attendance Checker (Date Selector)
            // ─────────────────────────────────────────────
            const dateSelector = document.getElementById('calendar-date-selector');
            
            const badgeEl = document.getElementById('status-main-badge-element');
            const leaveDetailsEl = document.getElementById('status-leave-details');
            const metricInEl = document.getElementById('metric-time-in');
            const metricOutEl = document.getElementById('metric-time-out');
            const metricLateEl = document.getElementById('metric-late');
            const metricUndertimeEl = document.getElementById('metric-undertime');
            const metricOvertimeEl = document.getElementById('metric-overtime');
            const metricTotalEl = document.getElementById('metric-total-hours');

            function updateDailyStatus(selectedDate) {
                if (!dateSelector) return;
                
                badgeEl.textContent = 'Loading...';
                badgeEl.className = 'status-main-badge status-notfiled';
                leaveDetailsEl.style.display = 'none';

                fetch(`{{ route("user.day_status") }}?date=${selectedDate}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Set status badge class contextually
                        const statLower = data.status.toLowerCase().replace(/\s+/g, '');
                        badgeEl.textContent = data.status;
                        
                        if (statLower === 'present' || statLower === 'late' || statLower === 'undertime') {
                            badgeEl.className = 'status-main-badge status-present';
                        } else if (statLower === 'absent') {
                            badgeEl.className = 'status-main-badge status-absent';
                        } else if (statLower === 'leave') {
                            badgeEl.className = 'status-main-badge status-leave';
                            if (data.leave_type) {
                                leaveDetailsEl.style.display = 'block';
                                leaveDetailsEl.innerHTML = `<strong>Type:</strong> ${data.leave_type}<br><strong>Reason:</strong> ${data.leave_reason || 'No reason provided'}`;
                            }
                        } else {
                            badgeEl.className = 'status-main-badge status-notfiled';
                        }

                        // Update metric labels
                        metricInEl.textContent = data.time_in;
                        metricOutEl.textContent = data.time_out;
                        metricLateEl.textContent = data.late_minutes > 0 ? `${data.late_minutes}m` : '0m';
                        metricUndertimeEl.textContent = data.undertime_minutes > 0 ? `${data.undertime_minutes}m` : '0m';
                        metricOvertimeEl.textContent = data.overtime_hours > 0 ? `${data.overtime_hours.toFixed(2)}h` : '0.00h';
                        metricTotalEl.textContent = data.total_hours > 0 ? `${data.total_hours.toFixed(2)}h` : '0.00h';
                    } else {
                        badgeEl.textContent = 'Error';
                        badgeEl.className = 'status-main-badge status-absent';
                    }
                })
                .catch(err => {
                    console.error(err);
                    badgeEl.textContent = 'Error';
                    badgeEl.className = 'status-main-badge status-absent';
                });
            }

            if (dateSelector) {
                dateSelector.addEventListener('change', function () {
                    updateDailyStatus(this.value);
                });
                
                // Trigger check on load for current selected date
                updateDailyStatus(dateSelector.value);
            }
        });
    </script>
@endsection