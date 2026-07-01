@extends('layouts.master')

@section('title', 'Analytics - VIA Architects Associates')

@section('styles')
    <style>

        .chart-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .chart-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .chart-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.07);
            padding: 1.5rem 1.5rem 1.2rem;
            border: 1px solid #e8eaf0;
        }

        .chart-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.2rem;
        }

        .chart-card-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .chart-filter-select {
            font-size: 0.8rem;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #475569;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            padding-right: 1.8rem;
        }

        .chart-legend {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.8rem;
            color: #64748b;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 4px;
        }

        .chart-canvas-wrap {
            position: relative;
            height: 220px;
        }

        .chart-canvas-wrap-sm {
            position: relative;
            height: 200px;
        }

        /* Dark mode */
        html.dark-mode .chart-card {
            background: var(--surface, #1e293b);
            border-color: var(--glass-border, #334155);
        }

        html.dark-mode .chart-card-title {
            color: #e2e8f0;
        }


        html.dark-mode .chart-filter-select {
            background: var(--surface, #1e293b);
            border-color: #334155;
            color: #94a3b8;
        }
    </style>
@endsection

@section('content')
    <div class="max-w-[1600px] mx-auto">

        <div class="content-header">
            <div>
                <h2 class="header-title">Analytics Dashboard</h2>
                <p class="header-subtitle">
                    <span class="subtitle-dot"></span>
                    Real-time monitoring and analysis of authentication events, security threats, and operational metrics.
                </p>
            </div>
        </div>

    
    {{-- Row 0: Payroll Expense Overview + Leave Request Status (moved from Dashboard) --}}
    <div class="chart-grid-2">

        {{-- Payroll Expense Overview --}}
        <div class="chart-card">
            <div class="chart-card-header">
                <h3 class="chart-card-title">Payroll Expense Overview</h3>
                <span style="font-size:0.78rem;padding:0.3rem 0.75rem;border-radius:8px;background:#f0fdf4;color:#16a34a;font-weight:600;">Monthly</span>
            </div>
            <div class="chart-canvas-wrap">
                <canvas id="payrollChart"></canvas>
            </div>
        </div>

        {{-- Leave Request Status --}}
        <div class="chart-card">
            <div class="chart-card-header">
                <h3 class="chart-card-title">Leave Request Status</h3>
                <span style="font-size:0.78rem;padding:0.3rem 0.75rem;border-radius:8px;background:#eef2ff;color:#4f46e5;font-weight:600;">Distribution</span>
            </div>
            <div class="chart-canvas-wrap" style="display:flex;justify-content:center;">
                <canvas id="leaveChart"></canvas>
            </div>
        </div>

    </div>

    {{-- Row 1: Login Activity + Security Threats Trends --}}
        <div class="chart-grid-2">

            {{-- Login Activity Bar Chart --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">Login Activity</h3>
                    <div style="display:flex;align-items:center;gap:1rem;">
                        <div class="chart-legend">
                            <span><span class="legend-dot" style="background:#22c55e;"></span>Successful Logins</span>
                            <span><span class="legend-dot" style="background:#ef4444;"></span>Failed Logins</span>
                        </div>
                        <select class="chart-filter-select" id="loginActivityFilter"
                            onchange="updateLoginChart(this.value)">
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="loginActivityChart"></canvas>
                </div>
            </div>

            {{-- Security Threats Trends --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">Security Threats Trends</h3>
                    <div style="display:flex;align-items:center;gap:1rem;">
                        <div class="chart-legend">
                            <span><span class="legend-dot" style="background:#ef4444;"></span>Brute Force Attempts</span>
                            <span><span class="legend-dot" style="background:#f59e0b;"></span>Account Lockouts</span>
                        </div>
                        <select class="chart-filter-select" id="threatFilter" onchange="updateThreatChart(this.value)">
                            <option value="6months">Last 6 Months</option>
                            <option value="3months">Last 3 Months</option>
                        </select>
                    </div>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="securityThreatsChart"></canvas>
                </div>
            </div>

        </div>

        {{-- Row 2: Browser Analytics + User Role Distribution + System Load --}}
        <div class="chart-grid-3">

            {{-- Real-time System Load --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">Real-time System Load</h3>
                </div>
                <div class="chart-canvas-wrap-sm">
                    <canvas id="systemLoadChart"></canvas>
                </div>
            </div>

            {{-- Browser Analytics Pie --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">Browser Analytics</h3>
                </div>
                <div class="chart-canvas-wrap-sm" style="display:flex;justify-content:center;">
                    <canvas id="browserChart"></canvas>
                </div>
            </div>

            {{-- User Role Distribution Donut --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">User Role Distribution</h3>
                </div>
                <div class="chart-canvas-wrap-sm" style="display:flex;justify-content:center;">
                    <canvas id="roleChart"></canvas>
                </div>
            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ─── 0. Payroll Expense Overview (real data from DB) ───────────────────
    const payrollCtx = document.getElementById('payrollChart').getContext('2d');
    new Chart(payrollCtx, {
        type: 'bar',
        data: {
            labels: @json($payrollLabels),
            datasets: [{
                label: 'Expense (₱)',
                data: @json($payrollData),
                backgroundColor: '#3b82f6',
                borderRadius: 8,
                barThickness: 22,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // ─── 0b. Leave Request Status Doughnut (real data from DB) ──────────────
    const leaveCtx = document.getElementById('leaveChart').getContext('2d');
    new Chart(leaveCtx, {
        type: 'doughnut',
        data: {
            labels: ['Approved', 'Pending', 'Rejected'],
            datasets: [{
                data: @json($leaveChartData),
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 20, font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 } }
                }
            }
        }
    });

    // ─── Shared Chart Defaults ───────────────────────────────────────────────
            Chart.defaults.font.family = 'Inter, sans-serif';
            Chart.defaults.font.size = 12;

            // ─── 1. Login Activity Bar Chart ─────────────────────────────────────────
            const loginWeeklyData = @json($loginWeeklyData);
            const loginMonthlyData = @json($loginMonthlyData);

            const loginCtx = document.getElementById('loginActivityChart').getContext('2d');
            let loginChart = new Chart(loginCtx, {
                type: 'bar',
                data: {
                    labels: loginWeeklyData.labels,
                    datasets: [
                        {
                            label: 'Successful Logins',
                            data: loginWeeklyData.success,
                            backgroundColor: '#22c55e',
                            borderRadius: 6,
                            barPercentage: 0.5,
                        },
                        {
                            label: 'Failed Logins',
                            data: loginWeeklyData.failed,
                            backgroundColor: '#ef4444',
                            borderRadius: 6,
                            barPercentage: 0.5,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            window.updateLoginChart = function (period) {
                const d = period === 'weekly' ? loginWeeklyData : loginMonthlyData;
                loginChart.data.labels = d.labels;
                loginChart.data.datasets[0].data = d.success;
                loginChart.data.datasets[1].data = d.failed;
                loginChart.update();
            };

            // ─── 2. Security Threats Trends Line Chart ───────────────────────────────
            const threat6m = @json($threat6m);
            const threat3m = @json($threat3m);

            const threatCtx = document.getElementById('securityThreatsChart').getContext('2d');
            let threatChart = new Chart(threatCtx, {
                type: 'line',
                data: {
                    labels: threat6m.labels,
                    datasets: [
                        {
                            label: 'Brute Force Attempts',
                            data: threat6m.brute,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239,68,68,0.10)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                        },
                        {
                            label: 'Account Lockouts',
                            data: threat6m.locks,
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245,158,11,0.08)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            window.updateThreatChart = function (period) {
                const d = period === '6months' ? threat6m : threat3m;
                threatChart.data.labels = d.labels;
                threatChart.data.datasets[0].data = d.brute;
                threatChart.data.datasets[1].data = d.locks;
                threatChart.update();
            };



            // ─── 4. Browser Analytics Pie ────────────────────────────────────────────
            const browserCtx = document.getElementById('browserChart').getContext('2d');
            new Chart(browserCtx, {
                type: 'pie',
                data: {
                    labels: @json($browserLabels),
                    datasets: [{
                        data: @json($browserData),
                        backgroundColor: ['#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#94a3b8', '#10b981'],
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 12, usePointStyle: true, font: { size: 11 } }
                        }
                    }
                }
            });

            // ─── 5. User Role Distribution Donut ────────────────────────────────────
            const roleCtx = document.getElementById('roleChart').getContext('2d');
            new Chart(roleCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Admin', 'Employee'],
                    datasets: [{
                        data: @json($roleChartData),
                        backgroundColor: ['#ef4444', '#3b82f6'],
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 12, usePointStyle: true, font: { size: 11 } }
                        }
                    }
                }
            });

            // ─── 6. Real-time System Load Line Chart ────────────────────────────────
            const loadCtx = document.getElementById('systemLoadChart').getContext('2d');
            
            // Generate initial dummy data (10 points)
            let loadData = [];
            let loadLabels = [];
            let now = new Date();
            for (let i = 9; i >= 0; i--) {
                let pastTime = new Date(now.getTime() - i * 5000);
                loadLabels.push(pastTime.toLocaleTimeString([], { hour12: false }));
                loadData.push(Math.floor(Math.random() * 40) + 20); // Random load between 20-60
            }

            let systemLoadChart = new Chart(loadCtx, {
                type: 'line',
                data: {
                    labels: loadLabels,
                    datasets: [{
                        label: 'System Load (%)',
                        data: loadData,
                        borderColor: '#8b5cf6', // Purple line
                        backgroundColor: 'rgba(139, 92, 246, 0.1)', // Purple fill
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            min: 0, 
                            max: 100, 
                            grid: { color: 'rgba(0,0,0,0.04)' } 
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { maxTicksLimit: 3 }
                        }
                    },
                    animation: {
                        duration: 400
                    }
                }
            });

            // Simulate real-time updates every 5 seconds
            setInterval(() => {
                let time = new Date();
                let timeStr = time.toLocaleTimeString([], { hour12: false });
                
                // Add new point that trends slightly from the last point
                let lastVal = loadData[loadData.length - 1];
                let change = Math.floor(Math.random() * 10) - 4; // -4 to +5
                let newLoad = Math.max(10, Math.min(95, lastVal + change));

                systemLoadChart.data.labels.push(timeStr);
                systemLoadChart.data.datasets[0].data.push(newLoad);

                // Keep only last 10 points
                if (systemLoadChart.data.labels.length > 10) {
                    systemLoadChart.data.labels.shift();
                    systemLoadChart.data.datasets[0].data.shift();
                }
                
                systemLoadChart.update('none'); // Update smoothly
            }, 5000);

        });
    </script>
@endsection