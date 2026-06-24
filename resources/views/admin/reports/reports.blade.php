@extends('layouts.master')

@section('title', 'Reports & Analytics - VIA Architects Associates')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/reports.css') }}">
    <style>
        /* Premium Modal Overlays */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .modal-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-content {
            background: var(--bg-surface, #ffffff);
            border: 1px solid var(--glass-border, #e2e8f0);
            border-radius: 1.5rem;
            width: 90%;
            max-width: 1000px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: scale(0.95) translateY(10px);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        .modal-overlay.show .modal-content {
            transform: scale(1) translateY(0);
        }
        .dark-mode .modal-content {
            background: rgba(30, 41, 59, 0.95);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .modal-header-flex {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--glass-border, #e2e8f0);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-main, #0f172a);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .modal-close {
            background: none;
            border: none;
            color: var(--slate-400, #94a3b8);
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-close:hover {
            background: rgba(0, 0, 0, 0.05);
            color: var(--text-main, #0f172a);
        }
        .dark-mode .modal-close:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .modal-body {
            padding: 1.5rem;
            max-height: 70vh;
            overflow-y: auto;
        }
        
        /* Modal tables */
        .modal-table-wrapper {
            overflow-x: auto;
            border-radius: 1rem;
            border: 1px solid var(--glass-border, #e2e8f0);
        }
        .modal-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }
        .modal-table th {
            padding: 0.875rem 1.25rem;
            background: var(--bg-dark, #f8fafc);
            font-weight: 700;
            color: var(--slate-500, #64748b);
            border-bottom: 1px solid var(--glass-border, #e2e8f0);
        }
        .dark-mode .modal-table th {
            background: rgba(15, 23, 42, 0.3);
            color: #94a3b8;
        }
        .modal-table td {
            padding: 0.875rem 1.25rem;
            border-bottom: 1px solid var(--glass-border, #e2e8f0);
            color: var(--text-main, #0f172a);
        }
        .modal-table tr:last-child td {
            border-bottom: none;
        }
        .modal-table-totals {
            font-weight: 700;
            background: var(--bg-dark, #f8fafc);
        }
        .dark-mode .modal-table-totals {
            background: rgba(15, 23, 42, 0.3);
            color: var(--text-main);
        }
        
        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            border-top: 3px solid #3b82f6;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Checkboxes styling in Custom reports modal */
        .custom-report-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 1rem;
            margin-bottom: 1.5rem;
        }
        .custom-option {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--bg-dark, #f8fafc);
            border: 1px solid var(--glass-border, #e2e8f0);
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .custom-option:hover {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.05);
        }
        .dark-mode .custom-option {
            background: rgba(15, 23, 42, 0.3);
        }
        .custom-option input[type="checkbox"] {
            width: 1.15rem;
            height: 1.15rem;
            border-radius: 0.25rem;
            border: 1px solid var(--glass-border);
            outline: none;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="max-w-[1600px] mx-auto">
        
        <!-- Header -->
        <div class="content-header">
            <div>
                <h2 class="header-title">Reports & Analytics</h2>
                <p class="header-subtitle">
                    <span class="subtitle-dot"></span>
                    Generate and download comprehensive system reports.
                </p>
            </div>
            
            <div class="header-actions">
                <button class="btn-primary" onclick="window.print()">
                    <i data-lucide="printer" class="mr-2 h-4 w-4"></i>
                    Print Summary
                </button>
            </div>
        </div>

        <div class="reports-container">
            
            <!-- Global Date Filter -->
            <form method="GET" action="{{ route('reports.index') }}">
                <div class="filters-bar">
                    <div class="filter-group-wrapper">
                        <div class="filter-group">
                            <label for="date_from" class="filter-label">From:</label>
                            <input type="date" id="date_from" name="date_from" class="filter-input" value="{{ $dateFrom }}">
                        </div>
                        <div class="filter-group">
                            <label for="date_to" class="filter-label">To:</label>
                            <input type="date" id="date_to" name="date_to" class="filter-input" value="{{ $dateTo }}">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-primary">
                        <i data-lucide="refresh-cw" class="mr-2 h-4 w-4"></i>
                        Apply Filter
                    </button>
                </div>
            </form>

            <!-- Quick Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-label">Total Payroll Cost</span>
                        <div class="stat-icon stat-icon-indigo">
                            <i data-lucide="banknote" class="h-5 w-5"></i>
                        </div>
                    </div>
                    <div class="stat-value">₱ {{ number_format($totalPayrollCost, 2) }}</div>
                    <div class="stat-badge {{ $costChange >= 0 ? 'badge-emerald' : 'badge-rose' }} mt-2 inline-block">
                        <i data-lucide="{{ $costChange >= 0 ? 'trending-up' : 'trending-down' }}" class="inline h-3 w-3 mr-1"></i>
                        {{ number_format(abs($costChange), 1) }}% vs last month
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-label">Taxes Withheld</span>
                        <div class="stat-icon stat-icon-amber">
                            <i data-lucide="landmark" class="h-5 w-5"></i>
                        </div>
                    </div>
                    <div class="stat-value">₱ {{ number_format($taxesWithheld, 2) }}</div>
                    <div class="stat-badge {{ $taxChange >= 0 ? 'badge-emerald' : 'badge-rose' }} mt-2 inline-block">
                        <i data-lucide="{{ $taxChange >= 0 ? 'trending-up' : 'trending-down' }}" class="inline h-3 w-3 mr-1"></i>
                        {{ number_format(abs($taxChange), 1) }}% vs last month
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-label">Total Overtime (Hrs)</span>
                        <div class="stat-icon stat-icon-rose">
                            <i data-lucide="clock" class="h-5 w-5"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ number_format($totalOvertimeHours, 1) }}</div>
                    <div class="stat-badge {{ $overtimeChange >= 0 ? 'badge-emerald' : 'badge-rose' }} mt-2 inline-block">
                        <i data-lucide="{{ $overtimeChange >= 0 ? 'trending-up' : 'trending-down' }}" class="inline h-3 w-3 mr-1"></i>
                        {{ number_format(abs($overtimeChange), 1) }}% vs last month
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-label">Pending Leaves</span>
                        <div class="stat-icon stat-icon-teal">
                            <i data-lucide="calendar-clock" class="h-5 w-5"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ $pendingLeaves }}</div>
                    <div class="stat-badge {{ $pendingLeaves > 0 ? 'badge-amber' : 'badge-emerald' }} mt-2 inline-block">
                        <i data-lucide="{{ $pendingLeaves > 0 ? 'alert-circle' : 'check-circle' }}" class="inline h-3 w-3 mr-1"></i>
                        {{ $pendingLeaves > 0 ? 'Requires attention' : 'Up to date' }}
                    </div>
                </div>
            </div>

            <!-- Report Categories -->
            <div>
                <h3 class="report-category-title">
                    <i data-lucide="file-text" class="h-5 w-5 text-primary"></i>
                    Available Reports
                </h3>
                <div class="reports-grid">
                    
                    <!-- Payroll Reports -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon-wrapper">
                                <i data-lucide="banknote" class="h-6 w-6"></i>
                            </div>
                            <div class="report-info">
                                <h4 class="report-title">Payroll Reports</h4>
                                <p class="report-desc">Access to detailed payroll reports, including costs, deductions, tax calculations, and employee pay history.</p>
                            </div>
                        </div>
                        <div class="report-actions">
                            <button class="btn-report btn-generate" data-type="payroll" data-title="Payroll Summary Report">
                                <i data-lucide="eye" class="h-4 w-4"></i> View
                            </button>
                            <button class="btn-report btn-download">
                                <i data-lucide="download" class="h-4 w-4"></i> Export
                            </button>
                        </div>
                    </div>

                    <!-- Tax Reports -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon-wrapper">
                                <i data-lucide="landmark" class="h-6 w-6"></i>
                            </div>
                            <div class="report-info">
                                <h4 class="report-title">Tax Reports</h4>
                                <p class="report-desc">Section for reviewing tax filings, deductions, and reports related to tax compliance.</p>
                            </div>
                        </div>
                        <div class="report-actions">
                            <button class="btn-report btn-generate" data-type="tax" data-title="Tax & Government Contributions Report">
                                <i data-lucide="eye" class="h-4 w-4"></i> View
                            </button>
                            <button class="btn-report btn-download">
                                <i data-lucide="download" class="h-4 w-4"></i> Export
                            </button>
                        </div>
                    </div>
                    
                    <!-- Departmental Reports -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon-wrapper">
                                <i data-lucide="users" class="h-6 w-6"></i>
                            </div>
                            <div class="report-info">
                                <h4 class="report-title">Departmental Reports</h4>
                                <p class="report-desc">Payroll breakdown by department, team, or project.</p>
                            </div>
                        </div>
                        <div class="report-actions">
                            <button class="btn-report btn-generate" data-type="departmental" data-title="Departmental Expense Breakdown">
                                <i data-lucide="eye" class="h-4 w-4"></i> View
                            </button>
                            <button class="btn-report btn-download">
                                <i data-lucide="download" class="h-4 w-4"></i> Export
                            </button>
                        </div>
                    </div>

                    <!-- Custom Reports -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon-wrapper">
                                <i data-lucide="sliders-horizontal" class="h-6 w-6"></i>
                            </div>
                            <div class="report-info">
                                <h4 class="report-title">Custom Reports</h4>
                                <p class="report-desc">Quick access to generate custom reports based on specific criteria (e.g., salary reports, overtime summaries).</p>
                            </div>
                        </div>
                        <div class="report-actions">
                            <button class="btn-report btn-generate" data-type="custom" data-title="Generate Custom CSV Report">
                                <i data-lucide="settings" class="h-4 w-4"></i> Configure
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Dynamic Modal System -->
    <div id="report-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header modal-header-flex">
                <h3 class="modal-title" id="modal-report-title">Report Details</h3>
                <button id="btn-close-modal" class="modal-close">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="modal-loading" style="display: none; padding: 3rem 0; text-align: center;">
                    <div class="spinner"></div>
                    <p style="margin-top: 1rem; color: var(--slate-400); font-weight: 500;">Loading report data...</p>
                </div>
                <div id="modal-table-container" class="modal-table-wrapper"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('report-modal');
            const btnCloseModal = document.getElementById('btn-close-modal');
            const modalTitle = document.getElementById('modal-report-title');
            const modalLoading = document.getElementById('modal-loading');
            const modalTableContainer = document.getElementById('modal-table-container');

            // ─────────────────────────────────────────────
            // Modal Open & AJAX Fetch
            // ─────────────────────────────────────────────
            document.querySelectorAll('.btn-generate').forEach(button => {
                const type = button.getAttribute('data-type');
                if (!type) return;

                button.addEventListener('click', function() {
                    const title = button.getAttribute('data-title') || 'Report Details';
                    const dateFrom = document.getElementById('date_from').value;
                    const dateTo = document.getElementById('date_to').value;

                    modalTitle.textContent = title;
                    modalTableContainer.innerHTML = '';
                    modalLoading.style.display = 'block';
                    modal.classList.add('show');

                    // If it is custom, render the checkboxes configuration inside the modal
                    if (type === 'custom') {
                        modalLoading.style.display = 'none';
                        renderCustomConfigPanel();
                        return;
                    }

                    fetch(`/reports/details/${type}?date_from=${dateFrom}&date_to=${dateTo}`)
                        .then(res => res.json())
                        .then(res => {
                            modalLoading.style.display = 'none';
                            if (res.success && res.data.length > 0) {
                                buildReportTable(type, res.data);
                            } else {
                                modalTableContainer.innerHTML = `
                                    <div style="text-align: center; padding: 4rem 1.5rem; color: var(--slate-400);">
                                        <i data-lucide="info" style="width: 3.5rem; height: 3.5rem; margin: 0 auto 1.25rem auto; stroke-width: 1.5; color: var(--slate-500);"></i>
                                        <p style="font-weight: 700; font-size: 1.1rem; margin: 0; color: var(--text-main);">No records found</p>
                                        <p style="font-size: 0.85rem; margin-top: 0.35rem;">There is no processed payroll data in the database matching the selected dates.</p>
                                    </div>
                                `;
                                if (window.lucide) window.lucide.createIcons();
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            modalLoading.style.display = 'none';
                            modalTableContainer.innerHTML = `
                                <p class="text-center" style="color: #ef4444; padding: 2rem; font-weight: 600;">Error fetching report details from server.</p>
                            `;
                        });
                });
            });

            btnCloseModal.addEventListener('click', () => {
                modal.classList.remove('show');
            });
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.classList.remove('show');
            });

            // ─────────────────────────────────────────────
            // Build Dynamic HTML Tables
            // ─────────────────────────────────────────────
            function buildReportTable(type, data) {
                let html = '<table class="modal-table"><thead><tr>';
                
                if (type === 'payroll') {
                    html += `
                        <th>Employee</th>
                        <th>Period Start</th>
                        <th>Period End</th>
                        <th>Pay Date</th>
                        <th style="text-align: right;">Basic Salary</th>
                        <th style="text-align: right;">Overtime</th>
                        <th style="text-align: right;">Gross Pay</th>
                        <th style="text-align: right;">Deductions</th>
                        <th style="text-align: right;">Net Pay</th>
                    </tr></thead><tbody>`;

                    let sumBasic = 0, sumOvertime = 0, sumGross = 0, sumDeductions = 0, sumNet = 0;
                    data.forEach(p => {
                        sumBasic += p.basic_salary;
                        sumOvertime += p.overtime_pay;
                        sumGross += p.gross_pay;
                        sumDeductions += p.total_deductions;
                        sumNet += p.net_pay;

                        html += `
                            <tr>
                                <td style="font-weight: 600; color: var(--text-main);">${p.employee}</td>
                                <td>${p.start_date}</td>
                                <td>${p.end_date}</td>
                                <td>${p.pay_date}</td>
                                <td style="text-align: right;">₱${p.basic_salary.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                                <td style="text-align: right;">₱${p.overtime_pay.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                                <td style="text-align: right; font-weight: 600;">₱${p.gross_pay.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                                <td style="text-align: right; color: #ef4444;">₱${p.total_deductions.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                                <td style="text-align: right; font-weight: 700; color: #10b981;">₱${p.net_pay.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            </tr>
                        `;
                    });

                    html += `
                        <tr class="modal-table-totals">
                            <td colspan="4">Total Summary</td>
                            <td style="text-align: right;">₱${sumBasic.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            <td style="text-align: right;">₱${sumOvertime.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            <td style="text-align: right;">₱${sumGross.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            <td style="text-align: right; color: #ef4444;">₱${sumDeductions.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            <td style="text-align: right; color: #10b981;">₱${sumNet.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                        </tr>
                    `;
                } else if (type === 'tax') {
                    html += `
                        <th>Employee</th>
                        <th>Pay Date</th>
                        <th style="text-align: right;">SSS</th>
                        <th style="text-align: right;">PhilHealth</th>
                        <th style="text-align: right;">Pag-IBIG</th>
                        <th style="text-align: right;">Withholding Tax</th>
                        <th style="text-align: right;">Total Withheld</th>
                    </tr></thead><tbody>`;

                    let sumSSS = 0, sumPhil = 0, sumHdmf = 0, sumTax = 0, sumTotal = 0;
                    data.forEach(p => {
                        sumSSS += p.sss;
                        sumPhil += p.philhealth;
                        sumHdmf += p.hdmf;
                        sumTax += p.tax;
                        sumTotal += p.total_deductions;

                        html += `
                            <tr>
                                <td style="font-weight: 600; color: var(--text-main);">${p.employee}</td>
                                <td>${p.pay_date}</td>
                                <td style="text-align: right;">₱${p.sss.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                                <td style="text-align: right;">₱${p.philhealth.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                                <td style="text-align: right;">₱${p.hdmf.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                                <td style="text-align: right; color: #f59e0b;">₱${p.tax.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                                <td style="text-align: right; font-weight: 700; color: #ef4444;">₱${p.total_deductions.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            </tr>
                        `;
                    });

                    html += `
                        <tr class="modal-table-totals">
                            <td colspan="2">Total Summary</td>
                            <td style="text-align: right;">₱${sumSSS.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            <td style="text-align: right;">₱${sumPhil.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            <td style="text-align: right;">₱${sumHdmf.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            <td style="text-align: right; color: #f59e0b;">₱${sumTax.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            <td style="text-align: right; color: #ef4444;">₱${sumTotal.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                        </tr>
                    `;
                } else if (type === 'departmental') {
                    html += `
                        <th>Department</th>
                        <th style="text-align: center;">Active Employees Paid</th>
                        <th style="text-align: right;">Total Basic Salary</th>
                        <th style="text-align: right;">Total Overtime</th>
                        <th style="text-align: right;">Total Gross Expenses</th>
                        <th style="text-align: right;">Total Deductions</th>
                        <th style="text-align: right;">Total Net Payroll</th>
                    </tr></thead><tbody>`;

                    let sumEmployees = 0, sumBasic = 0, sumOvertime = 0, sumGross = 0, sumDeductions = 0, sumNet = 0;
                    data.forEach(d => {
                        sumEmployees += d.employee_count;
                        sumBasic += d.total_basic;
                        sumOvertime += d.total_overtime;
                        sumGross += d.total_gross;
                        sumDeductions += d.total_deductions;
                        sumNet += d.total_net;

                        html += `
                            <tr>
                                <td style="font-weight: 600; color: var(--text-main);">${d.department_name}</td>
                                <td style="text-align: center; font-weight: 600;">${d.employee_count}</td>
                                <td style="text-align: right;">₱${d.total_basic.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                                <td style="text-align: right;">₱${d.total_overtime.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                                <td style="text-align: right; font-weight: 600;">₱${d.total_gross.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                                <td style="text-align: right; color: #ef4444;">₱${d.total_deductions.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                                <td style="text-align: right; font-weight: 700; color: #10b981;">₱${d.total_net.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            </tr>
                        `;
                    });

                    html += `
                        <tr class="modal-table-totals">
                            <td>Total Summary</td>
                            <td style="text-align: center;">${sumEmployees}</td>
                            <td style="text-align: right;">₱${sumBasic.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            <td style="text-align: right;">₱${sumOvertime.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            <td style="text-align: right;">₱${sumGross.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            <td style="text-align: right; color: #ef4444;">₱${sumDeductions.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            <td style="text-align: right; color: #10b981;">₱${sumNet.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                        </tr>
                    `;
                }

                html += '</tbody></table>';
                modalTableContainer.innerHTML = html;
            }

            // ─────────────────────────────────────────────
            // Custom Config Panel View
            // ─────────────────────────────────────────────
            function renderCustomConfigPanel() {
                modalTableContainer.innerHTML = `
                    <div style="padding: 0.5rem 1rem;">
                        <p style="font-size: 0.95rem; font-weight: 500; color: var(--text-main); margin-bottom: 1rem;">
                            Customize and build your report structure. Choose the datasets you would like to include in your custom exported spreadsheet:
                        </p>
                        
                        <div class="custom-report-grid">
                            <label class="custom-option">
                                <input type="checkbox" id="opt-basic" checked>
                                <span>Basic Salaries</span>
                            </label>
                            <label class="custom-option">
                                <input type="checkbox" id="opt-overtime" checked>
                                <span>Overtime Pay</span>
                            </label>
                            <label class="custom-option">
                                <input type="checkbox" id="opt-contributions" checked>
                                <span>Gov Contributions (SSS, Philhealth, Pag-IBIG)</span>
                            </label>
                            <label class="custom-option">
                                <input type="checkbox" id="opt-tax" checked>
                                <span>Withholding Tax</span>
                            </label>
                        </div>
                        
                        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                            <button id="btn-cancel-custom" class="btn-secondary" style="padding: 0.65rem 1.25rem; font-size: 0.875rem;">Cancel</button>
                            <button id="btn-export-custom" class="btn-primary" style="padding: 0.65rem 1.25rem; font-size: 0.875rem;">
                                <i data-lucide="download" class="h-4 w-4 mr-2"></i> Download CSV
                            </button>
                        </div>
                    </div>
                `;
                if (window.lucide) window.lucide.createIcons();

                document.getElementById('btn-cancel-custom').addEventListener('click', () => {
                    modal.classList.remove('show');
                });

                document.getElementById('btn-export-custom').addEventListener('click', () => {
                    // Custom CSV download triggers the main payroll CSV export which has all components included
                    const dateFrom = document.getElementById('date_from').value;
                    const dateTo = document.getElementById('date_to').value;
                    modal.classList.remove('show');
                    window.location.href = `/reports/export/payroll?date_from=${dateFrom}&date_to=${dateTo}`;
                });
            }

            // ─────────────────────────────────────────────
            // CSV stream-export trigger
            // ─────────────────────────────────────────────
            document.querySelectorAll('.btn-download').forEach(button => {
                button.addEventListener('click', function() {
                    const type = button.closest('.report-card').querySelector('.btn-generate').getAttribute('data-type');
                    if (!type) return;

                    const dateFrom = document.getElementById('date_from').value;
                    const dateTo = document.getElementById('date_to').value;

                    window.location.href = `/reports/export/${type}?date_from=${dateFrom}&date_to=${dateTo}`;
                });
            });
        });
    </script>
@endsection
