@extends('layouts.master')

@section('title', 'Government Remittance Report - VIA Architects Associates')

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
    .filter-bar select,
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

    /* Section badges */
    .section-badge {
        display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px;
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        margin-right: 0.25rem;
    }
    .badge-sss { background: #dbeafe; color: #1d4ed8; }
    .badge-ph { background: #dcfce7; color: #15803d; }
    .badge-hdmf { background: #fef9c3; color: #a16207; }

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
            <h2 class="page-title">Government Remittance Report</h2>
            <p class="page-subtitle">Statutory monthly returns showing both Employee (EE) and Employer (ER) contribution shares.</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('finance_admin.reports.government_remittance') }}">
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
                <label>Remittance Type</label>
                <select name="contribution_type">
                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Contributions</option>
                    <option value="sss" {{ $type === 'sss' ? 'selected' : '' }}>SSS Contributions</option>
                    <option value="philhealth" {{ $type === 'philhealth' ? 'selected' : '' }}>PhilHealth Premium</option>
                    <option value="hdmf" {{ $type === 'hdmf' ? 'selected' : '' }}>Pag-IBIG Fund</option>
                </select>
            </div>
            <div class="filter-group">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search Employee...">
            </div>
            <button type="submit" class="filter-btn">Apply Filter</button>
            <a href="{{ route('finance_admin.reports.government_remittance') }}" class="reset-btn">Reset</a>
            
            <a href="{{ route('finance_admin.reports.government_remittance', array_merge(request()->all(), ['export' => 'csv'])) }}" class="export-btn">
                <i data-lucide="file-spreadsheet" style="width: 16px; height: 16px;"></i>
                Export to CSV
            </a>
        </div>
    </form>

    {{-- Summary Cards --}}
    @php
        $eeTotal = 0;
        $erTotal = 0;
        if ($type === 'sss') {
            $eeTotal = $totals['sss_ee'];
            $erTotal = $totals['sss_er'];
        } elseif ($type === 'philhealth') {
            $eeTotal = $totals['ph_ee'];
            $erTotal = $totals['ph_er'];
        } elseif ($type === 'hdmf') {
            $eeTotal = $totals['hdmf_ee'];
            $erTotal = $totals['hdmf_er'];
        } else {
            $eeTotal = $totals['sss_ee'] + $totals['ph_ee'] + $totals['hdmf_ee'];
            $erTotal = $totals['sss_er'] + $totals['ph_er'] + $totals['hdmf_er'];
        }
    @endphp
    <div class="summary-grid">
        <div class="summary-card" style="--card-color: #3b82f6;">
            <div class="summary-card-label">Employee Share (EE) Total</div>
            <div class="summary-card-value money">₱{{ number_format($eeTotal, 2) }}</div>
        </div>
        <div class="summary-card" style="--card-color: #6366f1;">
            <div class="summary-card-label">Employer Share (ER) Total</div>
            <div class="summary-card-value money">₱{{ number_format($erTotal, 2) }}</div>
        </div>
        <div class="summary-card" style="--card-color: #10b981;">
            <div class="summary-card-label">Combined Returns Total</div>
            <div class="summary-card-value money">₱{{ number_format($eeTotal + $erTotal, 2) }}</div>
        </div>
    </div>

    {{-- Detailed List --}}
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Government Remittances Listing ({{ strtoupper($type) }})</span>
            <span style="font-size:0.8rem;color:#64748b;">{{ $paginatedRows->total() }} record(s) found</span>
        </div>
        <div class="table-wrap">
            @if($paginatedRows->count())
            <table>
                <thead>
                    @if($type === 'all')
                    <tr>
                        <th rowspan="2" style="vertical-align: middle;">#</th>
                        <th rowspan="2" style="vertical-align: middle;">Employee</th>
                        <th rowspan="2" style="vertical-align: middle;">Department</th>
                        <th rowspan="2" style="vertical-align: middle;">Pay Date</th>
                        <th colspan="3" class="text-right" style="background:#f8fafc; border-bottom: 2px solid #e2e8f0; text-align: center;"><span class="section-badge badge-sss">SSS</span></th>
                        <th colspan="3" class="text-right" style="background:#f8fafc; border-bottom: 2px solid #e2e8f0; text-align: center;"><span class="section-badge badge-ph">PhilHealth</span></th>
                        <th colspan="3" class="text-right" style="background:#f8fafc; border-bottom: 2px solid #e2e8f0; text-align: center;"><span class="section-badge badge-hdmf">Pag-IBIG</span></th>
                        <th colspan="3" class="text-right" style="background:#f1f5f9; border-bottom: 2px solid #cbd5e1; text-align: center;">Combined Shares</th>
                    </tr>
                    <tr>
                        <th class="text-right">EE</th>
                        <th class="text-right">ER</th>
                        <th class="text-right" style="font-weight:700;">Total</th>
                        <th class="text-right">EE</th>
                        <th class="text-right">ER</th>
                        <th class="text-right" style="font-weight:700;">Total</th>
                        <th class="text-right">EE</th>
                        <th class="text-right">ER</th>
                        <th class="text-right" style="font-weight:700;">Total</th>
                        <th class="text-right">EE</th>
                        <th class="text-right">ER</th>
                        <th class="text-right" style="font-weight:800; background:#f1f5f9;">Combined</th>
                    </tr>
                    @else
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Pay Date</th>
                        <th class="text-right">Employee Share (EE)</th>
                        <th class="text-right">Employer Share (ER)</th>
                        <th class="text-right" style="font-weight:700;">Combined Total</th>
                    </tr>
                    @endif
                </thead>
                <tbody>
                    @foreach($paginatedRows as $i => $row)
                    <tr>
                        <td style="color:#94a3b8;">{{ $paginatedRows->firstItem() + $i }}</td>
                        <td>
                            <strong style="display:block;">{{ $row['name'] }}</strong>
                            <span style="font-size:0.75rem;color:#64748b;">{{ $row['employee_number'] }}</span>
                        </td>
                        <td>{{ $row['department'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($row['pay_date'])->format('M d, Y') }}</td>
                        
                        @if($type === 'all')
                        <td class="text-right money">₱{{ number_format($row['sss_ee'], 2) }}</td>
                        <td class="text-right money">₱{{ number_format($row['sss_er'], 2) }}</td>
                        <td class="text-right money" style="font-weight:600; background:#f8fafc;">₱{{ number_format($row['sss_total'], 2) }}</td>
                        
                        <td class="text-right money">₱{{ number_format($row['ph_ee'], 2) }}</td>
                        <td class="text-right money">₱{{ number_format($row['ph_er'], 2) }}</td>
                        <td class="text-right money" style="font-weight:600; background:#f8fafc;">₱{{ number_format($row['ph_total'], 2) }}</td>
                        
                        <td class="text-right money">₱{{ number_format($row['hdmf_ee'], 2) }}</td>
                        <td class="text-right money">₱{{ number_format($row['hdmf_er'], 2) }}</td>
                        <td class="text-right money" style="font-weight:600; background:#f8fafc;">₱{{ number_format($row['hdmf_total'], 2) }}</td>

                        <td class="text-right money" style="font-weight:600;">₱{{ number_format($row['sss_ee'] + $row['ph_ee'] + $row['hdmf_ee'], 2) }}</td>
                        <td class="text-right money" style="font-weight:600;">₱{{ number_format($row['sss_er'] + $row['ph_er'] + $row['hdmf_er'], 2) }}</td>
                        <td class="text-right money" style="font-weight:800; background:#f1f5f9; color:#10b981;">₱{{ number_format($row['sss_total'] + $row['ph_total'] + $row['hdmf_total'], 2) }}</td>
                        @elseif($type === 'sss')
                        <td class="text-right money">₱{{ number_format($row['sss_ee'], 2) }}</td>
                        <td class="text-right money">₱{{ number_format($row['sss_er'], 2) }}</td>
                        <td class="text-right money" style="font-weight:700;color:#1d4ed8;">₱{{ number_format($row['sss_total'], 2) }}</td>
                        @elseif($type === 'philhealth')
                        <td class="text-right money">₱{{ number_format($row['ph_ee'], 2) }}</td>
                        <td class="text-right money">₱{{ number_format($row['ph_er'], 2) }}</td>
                        <td class="text-right money" style="font-weight:700;color:#15803d;">₱{{ number_format($row['ph_total'], 2) }}</td>
                        @elseif($type === 'hdmf')
                        <td class="text-right money">₱{{ number_format($row['hdmf_ee'], 2) }}</td>
                        <td class="text-right money">₱{{ number_format($row['hdmf_er'], 2) }}</td>
                        <td class="text-right money" style="font-weight:700;color:#a16207;">₱{{ number_format($row['hdmf_total'], 2) }}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @if($type === 'all')
                    <tr>
                        <td colspan="4" style="font-weight:700;">TOTALS (Current Page)</td>
                        <td class="text-right money tfoot">₱{{ number_format($paginatedRows->sum('sss_ee'), 2) }}</td>
                        <td class="text-right money tfoot">₱{{ number_format($paginatedRows->sum('sss_er'), 2) }}</td>
                        <td class="text-right money tfoot" style="background:#f1f5f9;">₱{{ number_format($paginatedRows->sum('sss_total'), 2) }}</td>
                        
                        <td class="text-right money tfoot">₱{{ number_format($paginatedRows->sum('ph_ee'), 2) }}</td>
                        <td class="text-right money tfoot">₱{{ number_format($paginatedRows->sum('ph_er'), 2) }}</td>
                        <td class="text-right money tfoot" style="background:#f1f5f9;">₱{{ number_format($paginatedRows->sum('ph_total'), 2) }}</td>
                        
                        <td class="text-right money tfoot">₱{{ number_format($paginatedRows->sum('hdmf_ee'), 2) }}</td>
                        <td class="text-right money tfoot">₱{{ number_format($paginatedRows->sum('hdmf_er'), 2) }}</td>
                        <td class="text-right money tfoot" style="background:#f1f5f9;">₱{{ number_format($paginatedRows->sum('hdmf_total'), 2) }}</td>

                        <td class="text-right money tfoot" style="color:#1e293b;">₱{{ number_format($paginatedRows->sum(fn($r) => $r['sss_ee'] + $r['ph_ee'] + $r['hdmf_ee']), 2) }}</td>
                        <td class="text-right money tfoot" style="color:#1e293b;">₱{{ number_format($paginatedRows->sum(fn($r) => $r['sss_er'] + $r['ph_er'] + $r['hdmf_er']), 2) }}</td>
                        <td class="text-right money tfoot" style="background:#cbd5e1; color:#0f172a;">₱{{ number_format($paginatedRows->sum(fn($r) => $r['sss_total'] + $r['ph_total'] + $r['hdmf_total']), 2) }}</td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="4" style="font-weight:700;">TOTALS (Current Page)</td>
                        <td class="text-right money tfoot">₱{{ number_format($paginatedRows->sum($type === 'sss' ? 'sss_ee' : ($type === 'philhealth' ? 'ph_ee' : 'hdmf_ee')), 2) }}</td>
                        <td class="text-right money tfoot">₱{{ number_format($paginatedRows->sum($type === 'sss' ? 'sss_er' : ($type === 'philhealth' ? 'ph_er' : 'hdmf_er')), 2) }}</td>
                        <td class="text-right money tfoot" style="color:#0f172a;">₱{{ number_format($paginatedRows->sum($type === 'sss' ? 'sss_total' : ($type === 'philhealth' ? 'ph_total' : 'hdmf_total')), 2) }}</td>
                    </tr>
                    @endif
                </tfoot>
            </table>
            @else
            <div class="empty-state">
                <i data-lucide="inbox" style="width:3rem;height:3rem;margin:0 auto 0.75rem;display:block;opacity:0.25;"></i>
                No contributions found for the selected period.
            </div>
            @endif
        </div>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $paginatedRows->links('vendor.pagination.numbers') }}
    </div>

</div>
@endsection
