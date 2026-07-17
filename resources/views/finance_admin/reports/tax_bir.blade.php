@extends('layouts.master')

@section('title', 'Tax (BIR) Report - VIA Architects Associates')

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
    .filter-bar input[type="date"],
    .filter-bar input[type="text"] {
        border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.5rem 0.75rem;
        font-size: 0.875rem; color: #1e293b; background: #f8fafc;
    }
    .filter-btn {
        padding: 0.5rem 1.25rem; border-radius: 8px; border: none;
        background: #1e293b; color: #fff; font-size: 0.875rem; font-weight: 600;
        cursor: pointer; transition: background 0.2s;
    }
    .filter-btn:hover { background: #0f172a; }
    .reset-btn {
        padding: 0.5rem 1.25rem; border-radius: 8px; border: 1px solid #e2e8f0;
        background: #fff; color: #64748b; font-size: 0.875rem; font-weight: 600;
        text-decoration: none; display: inline-flex; align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .reset-btn:hover { background: #f8fafc; border-color: #cbd5e1; }
    
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

    /* ── Panel / Table ────────────────────────────── */
    .panel {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.04);
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

    .empty-state {
        padding: 3rem; text-align: center; color: #94a3b8;
    }
</style>
@endsection

@section('content')
<div class="reports-page">

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h2 class="page-title">Tax (BIR) Report</h2>
            <p class="page-subtitle">BIR withholding tax summary based on taxable compensation income and statutory exemptions.</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('finance_admin.reports.tax_bir') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label>From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}">
            </div>
            <div class="filter-group">
                <label>To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}">
            </div>
            <div class="filter-group">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search Employee...">
            </div>
            <button type="submit" class="filter-btn">Apply Filter</button>
            <a href="{{ route('finance_admin.reports.tax_bir') }}" class="reset-btn">Reset</a>
            
            <a href="{{ route('finance_admin.reports.tax_bir', array_merge(request()->all(), ['export' => 'csv'])) }}" class="export-btn">
                <i data-lucide="file-spreadsheet" style="width: 16px; height: 16px;"></i>
                Export to CSV
            </a>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="summary-grid">
        <div class="summary-card" style="--card-color: #64748b;">
            <div class="summary-card-label">Gross Compensation</div>
            <div class="summary-card-value money">₱{{ number_format($totalGross, 2) }}</div>
        </div>
        <div class="summary-card" style="--card-color: #ef4444;">
            <div class="summary-card-label">Statutory Deductions (Non-taxable)</div>
            <div class="summary-card-value money">₱{{ number_format($totalStatutory, 2) }}</div>
        </div>
        <div class="summary-card" style="--card-color: #3b82f6;">
            <div class="summary-card-label">Taxable Compensation</div>
            <div class="summary-card-value money">₱{{ number_format($totalTaxable, 2) }}</div>
        </div>
        <div class="summary-card" style="--card-color: #ef4444;">
            <div class="summary-card-label">BIR Withholding Tax Total</div>
            <div class="summary-card-value money">₱{{ number_format($totalTax, 2) }}</div>
        </div>
    </div>

    {{-- Detailed List --}}
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">BIR Form 2316 Deductions Summary</span>
            <span style="font-size:0.8rem;color:#64748b;">{{ $payrolls->total() }} record(s) found</span>
        </div>
        <div class="table-wrap">
            @if($payrolls->count())
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Pay Date</th>
                        <th class="text-right">Gross Income</th>
                        <th class="text-right">Statutory Deductions</th>
                        <th class="text-right">Taxable Income</th>
                        <th class="text-right" style="color:#ef4444;">Withholding Tax</th>
                    </tr>
                </thead>
                <tbody>
                    @php $pageStatutoryTotal = 0; @endphp
                    @foreach($payrolls as $i => $p)
                    @php
                        $statutory = (float)$p->sss + (float)$p->philhealth + (float)$p->hdmf;
                        $taxable = max(0.0, (float)$p->gross_pay - $statutory);
                        $pageStatutoryTotal += $statutory;
                    @endphp
                    <tr>
                        <td style="color:#94a3b8;">{{ $payrolls->firstItem() + $i }}</td>
                        <td>
                            <strong style="display:block;">{{ $p->employee ? $p->employee->name : 'Unknown' }}</strong>
                            <span style="font-size:0.75rem;color:#64748b;">{{ $p->employee->employee_number ?? '—' }}</span>
                        </td>
                        <td>{{ $p->employee->department->department_name ?? '—' }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->payroll_date)->format('M d, Y') }}</td>
                        <td class="text-right money">₱{{ number_format($p->gross_pay, 2) }}</td>
                        <td class="text-right money" style="color:#64748b;">₱{{ number_format($statutory, 2) }}</td>
                        <td class="text-right money" style="font-weight:600;">₱{{ number_format($taxable, 2) }}</td>
                        <td class="text-right money" style="font-weight:700;color:#ef4444;">₱{{ number_format($p->tax, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="font-weight:700;">TOTALS (Current Page)</td>
                        <td class="text-right money tfoot">₱{{ number_format($payrolls->sum('gross_pay'), 2) }}</td>
                        <td class="text-right money tfoot" style="color:#64748b;">₱{{ number_format($pageStatutoryTotal, 2) }}</td>
                        <td class="text-right money tfoot">₱{{ number_format(max(0, $payrolls->sum('gross_pay') - $pageStatutoryTotal), 2) }}</td>
                        <td class="text-right money tfoot" style="color:#b91c1c;">₱{{ number_format($payrolls->sum('tax'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            @else
            <div class="empty-state">
                <i data-lucide="inbox" style="width:3rem;height:3rem;margin:0 auto 0.75rem;display:block;opacity:0.25;"></i>
                No tax entries found for the selected filters.
            </div>
            @endif
        </div>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $payrolls->links('vendor.pagination.numbers') }}
    </div>

</div>
@endsection
