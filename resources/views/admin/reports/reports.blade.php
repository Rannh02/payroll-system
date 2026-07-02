@extends('layouts.master')

@section('title', 'Reports & Analytics - VIA Architects Associates')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/reports.css') }}">
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
    <script src="{{ asset('js/admin/reports.js') }}"></script>
@endsection
