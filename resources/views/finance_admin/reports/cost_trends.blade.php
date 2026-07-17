@extends('layouts.master')

@section('title', 'Payroll Cost Trends - VIA Architects Associates')

@section('styles')
<style>
    .reports-page { max-width: 1400px; margin: 0 auto; }

    /* ── Page Header ─────────────────────────────── */
    .page-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;
    }
    .page-title { font-size: 1.75rem; font-weight: 800; color: #1e293b; margin: 0; }
    .page-subtitle { font-size: 0.875rem; color: #64748b; margin-top: 0.25rem; }

    /* ── Filter Bar ──────────────────────────────── */
    .filter-bar {
        display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 1rem 1.25rem; margin-bottom: 2rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .filter-group { display: flex; align-items: center; gap: 0.5rem; }
    .filter-bar label { font-size: 0.8rem; font-weight: 600; color: #64748b; }
    .filter-bar select {
        border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.5rem 0.75rem;
        font-size: 0.875rem; color: #1e293b; background: #f8fafc;
    }
    .filter-btn {
        padding: 0.5rem 1.25rem; border-radius: 8px; border: none;
        background: #1e293b; color: #fff; font-size: 0.875rem; font-weight: 600;
        cursor: pointer; transition: background 0.2s;
    }
    .filter-btn:hover { background: #0f172a; }
    
    .export-btn {
        margin-left: auto; padding: 0.5rem 1.25rem; border-radius: 8px; border: none;
        background: #10b981; color: #fff; font-size: 0.875rem; font-weight: 600;
        cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem;
        text-decoration: none; transition: background 0.2s;
    }
    .export-btn:hover { background: #059669; }

    /* ── Summary Cards ───────────────────────────── */
    .summary-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem; margin-bottom: 2rem;
    }
    .summary-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        border-top: 3px solid var(--card-color, #1e293b);
    }
    .summary-card-label { font-size: 0.75rem; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: 0.05em; }
    .summary-card-value { font-size: 1.5rem; font-weight: 800; color: #1e293b;
        margin-top: 0.35rem; }

    /* ── Charts Grid ─────────────────────────────── */
    .charts-grid {
        display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;
    }
    @media (max-width: 992px) {
        .charts-grid { grid-template-columns: 1fr; }
    }
    .chart-container {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        padding: 1.5rem; box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .chart-title { font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem; }

    /* ── Panel / Table ────────────────────────────── */
    .panel {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        margin-bottom: 2rem;
    }
    .panel-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1.1rem 1.5rem; border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .panel-title { font-size: 1rem; font-weight: 700; color: #1e293b; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #f1f5f9; }
    th {
        padding: 0.75rem 1rem; text-align: left; font-size: 0.78rem; font-weight: 700;
        color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;
        white-space: nowrap;
    }
    td {
        padding: 0.85rem 1rem; font-size: 0.875rem; color: #1e293b;
        border-bottom: 1px solid #f1f5f9; vertical-align: middle;
    }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #f8fafc; }
    .tfoot td {
        font-weight: 800; background: #f1f5f9; border-top: 2px solid #e2e8f0;
        font-size: 0.875rem;
    }
    .text-right { text-align: right; }
    .money { font-variant-numeric: tabular-nums; }
</style>

<!-- Load Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="reports-page">

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h2 class="page-title">Payroll Cost Trends</h2>
            <p class="page-subtitle">Annual payroll analysis, dynamic month-by-month metrics, and departmental allocation trends.</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('finance_admin.reports.cost_trends') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label>Select Year</label>
                <select name="year">
                    @for($y = \Carbon\Carbon::now()->year; $y >= \Carbon\Carbon::now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="filter-btn">Apply Filter</button>
            
            <a href="{{ route('finance_admin.reports.cost_trends', array_merge(request()->all(), ['export' => 'csv'])) }}" class="export-btn">
                <i data-lucide="file-spreadsheet" style="width: 16px; height: 16px;"></i>
                Export to CSV
            </a>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="summary-grid">
        <div class="summary-card" style="--card-color: #1e293b;">
            <div class="summary-card-label">YTD Gross Expense</div>
            <div class="summary-card-value money">₱{{ number_format($totalGrossExpense, 2) }}</div>
        </div>
        <div class="summary-card" style="--card-color: #10b981;">
            <div class="summary-card-label">YTD Net Disbursed</div>
            <div class="summary-card-value money">₱{{ number_format($totalNetExpense, 2) }}</div>
        </div>
        <div class="summary-card" style="--card-color: #6366f1;">
            <div class="summary-card-label">Average Monthly Cost</div>
            <div class="summary-card-value money">₱{{ number_format($avgMonthlyCost, 2) }}</div>
        </div>
        <div class="summary-card" style="--card-color: #ef4444;">
            <div class="summary-card-label">YoY Cost Change</div>
            <div class="summary-card-value money" style="color: {{ $yoyChangePercent > 0 ? '#ef4444' : ($yoyChangePercent < 0 ? '#10b981' : '#1e293b') }}">
                {{ $yoyChangePercent > 0 ? '+' : '' }}{{ number_format($yoyChangePercent, 2) }}%
            </div>
        </div>
    </div>

    {{-- Charts Grid --}}
    <div class="charts-grid">
        {{-- Trend Line Chart --}}
        <div class="chart-container">
            <h3 class="chart-title">Monthly Expense Breakdown ({{ $year }})</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        {{-- Department Pie Chart --}}
        <div class="chart-container">
            <h3 class="chart-title">Department Distribution</h3>
            <div style="position: relative; height: 300px; width: 100%; display: flex; justify-content: center; align-items: center;">
                @if($deptDistribution->count())
                    <canvas id="deptDistributionChart"></canvas>
                @else
                    <span style="color:#94a3b8; font-size: 0.85rem;">No data for department cost distribution</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Monthly Summary Panel --}}
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Month-by-Month Expenditure Summary</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th class="text-right">Employee Count</th>
                        <th class="text-right">Total Basic Salary</th>
                        <th class="text-right">Total Overtime Pay</th>
                        <th class="text-right">Total Gross Pay</th>
                        <th class="text-right">Total Deductions</th>
                        <th class="text-right">Total Net Pay</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyData as $row)
                    <tr>
                        <td style="font-weight: 600;">{{ $row['month_name'] }}</td>
                        <td class="text-right money">{{ $row['employee_count'] }}</td>
                        <td class="text-right money">₱{{ number_format($row['basic_salary'], 2) }}</td>
                        <td class="text-right money">₱{{ number_format($row['overtime_pay'], 2) }}</td>
                        <td class="text-right money" style="font-weight:600;">₱{{ number_format($row['gross_pay'], 2) }}</td>
                        <td class="text-right money" style="color:#ef4444;">₱{{ number_format($row['deductions'], 2) }}</td>
                        <td class="text-right money" style="font-weight:700; color:#10b981;">₱{{ number_format($row['net_pay'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="font-weight:700;">TOTALS</td>
                        <td class="text-right money tfoot">{{ $monthlyData->sum('employee_count') }}</td>
                        <td class="text-right money tfoot">₱{{ number_format($monthlyData->sum('basic_salary'), 2) }}</td>
                        <td class="text-right money tfoot">₱{{ number_format($monthlyData->sum('overtime_pay'), 2) }}</td>
                        <td class="text-right money tfoot">₱{{ number_format($monthlyData->sum('gross_pay'), 2) }}</td>
                        <td class="text-right money tfoot" style="color:#b91c1c;">₱{{ number_format($monthlyData->sum('deductions'), 2) }}</td>
                        <td class="text-right money tfoot" style="color:#15803d;">₱{{ number_format($monthlyData->sum('net_pay'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Prepare Monthly Trend Data
        const monthlyData = @json($monthlyData->values()->toArray());
        const months = monthlyData.map(m => m.month_name);
        const grossPay = monthlyData.map(m => m.gross_pay);
        const netPay = monthlyData.map(m => m.net_pay);
        const deductions = monthlyData.map(m => m.deductions);

        const ctxTrend = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Gross Pay',
                        data: grossPay,
                        backgroundColor: 'rgba(99, 102, 241, 0.65)',
                        borderColor: '#6366f1',
                        borderWidth: 1
                    },
                    {
                        label: 'Net Pay',
                        data: netPay,
                        backgroundColor: 'rgba(16, 185, 129, 0.65)',
                        borderColor: '#10b981',
                        borderWidth: 1
                    },
                    {
                        label: 'Deductions',
                        type: 'line',
                        data: deductions,
                        borderColor: '#ef4444',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += '₱' + context.parsed.y.toLocaleString();
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Prepare Department Distribution Data
        @if($deptDistribution->count())
            const deptData = @json($deptDistribution->toArray());
            const deptLabels = deptData.map(d => d.department_name);
            const deptGross = deptData.map(d => d.total_gross);

            const ctxDept = document.getElementById('deptDistributionChart').getContext('2d');
            new Chart(ctxDept, {
                type: 'doughnut',
                data: {
                    labels: deptLabels,
                    datasets: [{
                        data: deptGross,
                        backgroundColor: [
                            '#3b82f6', '#10b981', '#6366f1', '#f59e0b', '#ec4899', '#8b5cf6', '#14b8a6'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: { size: 10 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += '₱' + context.parsed.toLocaleString();
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        @endif
    });
</script>
@endsection
