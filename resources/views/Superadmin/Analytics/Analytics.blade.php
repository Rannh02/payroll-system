@extends('Superadmin.layouts.master')

@section('title', 'Analytics - Control Deck')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/superadmin/analytics.css') }}">
@endsection

@section('content')
    <div style="max-width: 1600px; margin: 0 auto; display: flex; flex-direction: column; gap: 2rem;">

        <div class="content-header">
            <div>
                <h2 class="header-title">Analytics Dashboard</h2>
                <p class="header-subtitle">
                    <span class="subtitle-dot"></span>
                    Comprehensive monitoring and analysis of security, operations, and financial metrics.
                </p>
            </div>
        </div>

        {{-- Row 1: Login Metrics --}}
        <div class="chart-grid-2">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">Login Attempts Over Time</h3>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="loginAttemptsChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">Successful vs Failed Logins</h3>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="loginStatusChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Row 2: Roles & Security --}}
        <div class="chart-grid-2">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">User Roles Distribution</h3>
                </div>
                <div class="chart-canvas-wrap-circular">
                    <canvas id="userRolesChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">Security Incidents by Type</h3>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="securityIncidentsChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Row 3: Audit Logs & Payroll Expenses --}}
        <div class="chart-grid-2">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">Audit Logs by Module</h3>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="auditLogsChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">Payroll Expenses by Department</h3>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="payrollDeptChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Row 4: Payroll Trend & Salary --}}
        <div class="chart-grid-2">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">Payroll Trend</h3>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="payrollTrendChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">Salary Distribution</h3>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="salaryDistChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Row 5: Attendance & Leaves --}}
        <div class="chart-grid-2">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">Employee Attendance Rate</h3>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="attendanceRateChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">Leave Request Status</h3>
                </div>
                <div class="chart-canvas-wrap-circular">
                    <canvas id="leaveStatusChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Row 6: Deductions & Benefits --}}
        <div class="chart-grid-2">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">11. Tax Deductions</h3>
                </div>
                <div class="chart-canvas-wrap-circular">
                    <canvas id="taxDeductionsChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">12. Benefits Costs (Govt. Contributions)</h3>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="benefitsCostsChart"></canvas>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/superadmin/analytics.js') }}"></script>
@endsection