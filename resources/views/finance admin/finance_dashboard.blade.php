@extends('layouts.master')

@section('title', 'Finance Dashboard - VIA Architects Associates')

@section('styles')
    <style>
        .finance-main-content {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            max-width: 1600px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Header */
        .finance-header {
            margin-bottom: 1rem;
        }

        .finance-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 0.5rem 0;
        }
        .finance-subtitle {
            font-size: 0.95rem;
            color: #64748b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .subtitle-dot {
            width: 8px;
            height: 8px;
            background: #4f46e5;
            border-radius: 50%;
            display: inline-block;
        }
         /* stat Grid */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.07);
            border: 1px solid #e8eaf0;
            display: grid;
            grid-template-columns: 60px 1fr;
            grid-template-rows: auto auto auto;
            gap: 1rem 1.5rem;
            transition: all 0.3s ease;
            align-items: start;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            grid-column: 1;
            grid-row: 1;
        }
        .stat-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            grid-column: 1;
            grid-row: 2;
            width: 60px;
            line-height: 1.2;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000000ff;
            font-size: 1.8rem;
            font-weight: 600;
        }
        .stat-description {
            font-size: 0.8rem;
            color: #94a3b8;
            line-height: 1.4;
            grid-column: 2;
            grid-row: 3;
            margin-top: 0.5rem;
        }
        
        .activity-container {
            width: 200%;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.07);
            padding: 1.5rem;
            border: 1px solid #e8eaf0;
        }
        /* Chart Card */
        .chart-card {
            width: 100%;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.07);
            padding: 1.5rem;
            border: 1px solid #e8eaf0;
        }

        .chart-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .chart-card-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        /* Reports Table */
        .reports-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .reports-table thead tr {
            border-bottom: px solid #e2e8f0;
        }

        .reports-table th {
            text-align: left;
            padding: 1rem;
            font-size: 0.85rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: #f8fafc;
        }

        .reports-table td {
            padding: 1rem;
            border-bottom: 1px solid #e8eaf0;
            font-size: 0.9rem;
            color: #1e293b;
        }

        .reports-table tr:hover {
            background: #f8fafc;
        }

        .report-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-processing {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-pending {
            background: #fecaca;
            color: #7f1d1d;
        }

        .badge-approved {
            background: #bbf7d0;
            color: #166534;
        }

        .badge-compliance {
            background: #bfdbfe;
            color: #1e40af;
        }

        .reports-icon {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #4f46e5;
        }

        /* Dark Mode */
        html.dark-mode .chart-card {
            background: var(--surface, #1e293b);
            border-color: var(--glass-border, #334155);
        }

        html.dark-mode .finance-title,
        html.dark-mode .stat-value,
        html.dark-mode .chart-card-title {
            color: #e2e8f0;
        }

        html.dark-mode .finance-subtitle,
        html.dark-mode .stat-label,
        html.dark-mode .stat-description {
            color: #4c5460ff;
        }

        html.dark-mode .stat-card {
            background: var(--surface, #1e293b);
            border-color: var(--glass-border, #334155);
        }

        html.dark-mode .stat-icon.primary,
        html.dark-mode .stat-icon.success,
        html.dark-mode .stat-icon.warning,
        html.dark-mode .stat-icon.danger {
            background: #cbd5e1;
            color: #1e293b;
        }

        html.dark-mode .reports-table th {
            background: #334155;
            color: #cbd5e1;
        }

        html.dark-mode .reports-table td {
            color: #e2e8f0;
            border-bottom-color: #334155;
        }

        html.dark-mode .reports-table tr:hover {
            background: #334155;
        }

        @media (max-width: 768px) {
            .stat-grid {
                grid-template-columns: 1fr;
            }

            .stat-card {
                grid-template-columns: 50px 1fr;
                padding: 1.5rem;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }

            .stat-value {
                font-size: 1.75rem;
            }

            .finance-main-content {
                padding: 1rem;
                gap: 1rem;
            }

            .finance-title {
                font-size: 1.5rem;
            }
        }
</style>
@endsection

@section('content')
    <div class="finance-main-content">
        <!-- Header -->
        <div class="finance-header">
            <h2 class="finance-title">Welcome back, Finance Admin!</h2>
        </div>

        <!-- stat Grid -->
        <div class="stat-grid">
            <!-- Total Payroll Cost -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="dollar-sign" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Total Payroll Cost</h3>
                <h1 class="stat-description">Year-to-date expenses</h1>
                <h2 class="stat-value">₱{{ number_format($totalPayrollCost, 2) }}</h2>
            </div>

            <!-- Net Pay Disbursed -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="check-circle" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Net Pay Disbursed</h3>
                <p class="stat-value">₱{{ number_format($netPayDisbursed, 2) }}</p>
                <p class="stat-description">After deductions released</p>
            </div>

            <!-- Pending Payroll Runs -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="clock" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Pending Runs</h3>
                <p class="stat-value">{{ $pendingRuns }}</p>
                <p class="stat-description">Awaiting processing this month</p>
            </div>

            <!-- Payroll Runs Awaiting Approval -->
            <a href="{{ route('finance_admin.payroll.pending_approvals') }}" style="text-decoration:none;">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="alert-circle" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Awaiting Approval</h3>
                <p class="stat-value">{{ $awaitingApproval }}</p>
                <p class="stat-description">Requires sign-off</p>
            </div>
            </a>

            <!-- Total Deductions (Current Period) -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="bar-chart-2" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Total Deductions</h3>
                <p class="stat-value">₱{{ number_format($totalDeductions, 2) }}</p>
                <p class="stat-description">SSS, PhilHealth, etc. this month</p>
            </div>

            <!-- Total Government Contributions Remitted -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="building" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Gov. Contributions</h3>
                <p class="stat-value">₱{{ number_format($govContributions, 2) }}</p>
                <p class="stat-description">Compliance tracking YTD</p>
            </div>

            <!-- Pending Leave Claims -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="clipboard" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Pending Claims</h3>
                <p class="stat-value">{{ $pendingClaims }}</p>
                <p class="stat-description">Awaiting processing</p>
            </div>

            <!-- Payroll Discrepancies Flagged -->
            <a href="{{ route('finance_admin.payroll.discrepancy_review') }}" style="text-decoration:none;">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="alert-triangle" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Flagged Discrepancies</h3>
                <p class="stat-value">{{ $flaggedDiscrepancies }}</p>
                <p class="stat-description">Needs review</p>
            </div>
            </a>

            <!-- Upcoming Remittance Deadlines -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="calendar" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Upcoming Deadline</h3>
                <p class="stat-value">{{ $upcomingDeadlines }}</p>
                <p class="stat-description">Next remittance deadline</p>
            </div>

            <!-- Year-to-Date Payroll Expense -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="trending-up" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">YTD Payroll Expense</h3>
                <p class="stat-value">₱{{ number_format($ytdPayrollExpense, 2) }}</p>
                <p class="stat-description">Cumulative gross cost</p>
            </div>
        </div>

         <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-top: 1.5rem; margin-bottom: 1.5rem;">
            <div class="activity-container" style="margin-top: 0;">
                <div class="activity-header">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h3 class="chart-card-title"> Analytics & Reports</h3>
                    </div>

                    <table class="reports-table">
                        <thead>
                            <tr>
                                <th>Report</th>
                                <th>Contents</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="reports-icon">
                                             Payroll Summary Report
                                    </div>
                                </td>
                                <td>Per cutoff/month — gross pay, deductions, net pay by employee/department</td>
                                <td><span class="report-badge badge-processing">Processing</span></td>
                                <td><a href="#" style="color: #4f46e5; text-decoration: none;">View</a></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="reports-icon">
                                         Government Contributions Report
                                    </div>
                                </td>
                                <td>SSS/PhilHealth/Pag-IBIG/BIR breakdown, ready for remittance filing</td>
                                <td><span class="report-badge badge-approved">Approved</span></td>
                                <td><a href="#" style="color: #4f46e5; text-decoration: none;">Download</a></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="reports-icon">
                                         Deductions Report
                                    </div>
                                </td>
                                <td>Loans, tardiness deductions, other withholdings itemized</td>
                                <td><span class="report-badge badge-pending">Pending</span></td>
                                <td><a href="#" style="color: #4f46e5; text-decoration: none;">Review</a></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="reports-icon">
                                        Payroll Cost Trend
                                    </div>
                                </td>
                                <td>Month-over-month or YTD payroll expense (line chart)</td>
                                <td><span class="report-badge badge-compliance">Compliance</span></td>
                                <td><a href="#" style="color: #4f46e5; text-decoration: none;">View Chart</a></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="reports-icon">
                                        Department Payroll Cost Breakdown
                                    </div>
                                </td>
                                <td>Cost distribution across departments</td>
                                <td><span class="report-badge badge-approved">Approved</span></td>
                                <td><a href="#" style="color: #4f46e5; text-decoration: none;">View</a></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="reports-icon">
                                         Tax Report (BIR)
                                    </div>
                                </td>
                                <td>Withholding tax summary per employee, for compliance filing</td>
                                <td><span class="report-badge badge-compliance">Compliance</span></td>
                                <td><a href="#" style="color: #4f46e5; text-decoration: none;">Export</a></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="reports-icon">
                                         Reimbursement/Allowance Report
                                    </div>
                                </td>
                                <td>Claims processed, pending, denied</td>
                                <td><span class="report-badge badge-pending">Pending</span></td>
                                <td><a href="#" style="color: #4f46e5; text-decoration: none;">Review</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Add active class based on current route (if needed)
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', function(e) {
                document.querySelectorAll('.sidebar-menu a').forEach(a => a.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
@endsection
