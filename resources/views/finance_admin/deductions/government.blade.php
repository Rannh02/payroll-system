@extends('layouts.master')

@section('title', 'Government Contributions - VIA Architects Associates')

@section('styles')
<style>
    .deductions-page { max-width: 1400px; margin: 0 auto; }

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
    .filter-bar label { font-size: 0.8rem; font-weight: 600; color: #64748b; }
    .filter-bar input[type="date"] {
        border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.5rem 0.75rem;
        font-size: 0.875rem; color: #1e293b; background: #f8fafc; cursor: pointer;
    }
    .filter-btn {
        padding: 0.5rem 1.25rem; border-radius: 8px; border: none;
        background: #1e293b; color: #fff; font-size: 0.875rem; font-weight: 600;
        cursor: pointer; transition: background 0.2s;
    }
    .filter-btn:hover { background: #0f172a; }

    /* ── Summary Cards ───────────────────────────── */
    .summary-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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

    /* ── Tabs ─────────────────────────────────────── */
    .tabs { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .tab-btn {
        padding: 0.55rem 1.25rem; border-radius: 8px; border: 1px solid #e2e8f0;
        background: #f8fafc; font-size: 0.875rem; font-weight: 600; color: #64748b;
        cursor: pointer; transition: all 0.2s;
    }
    .tab-btn.active, .tab-btn:hover {
        background: #1e293b; color: #fff; border-color: #1e293b;
    }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    /* ── Table ─────────────────────────────────────── */
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

    /* ── Rate badge ─────────────────────────────────── */
    .rate-badge {
        display: inline-block; padding: 0.2rem 0.65rem; border-radius: 6px;
        font-size: 0.75rem; font-weight: 700;
    }
    .rate-sss   { background: #dbeafe; color: #1d4ed8; }
    .rate-ph    { background: #dcfce7; color: #15803d; }
    .rate-hdmf  { background: #fef9c3; color: #a16207; }
    .rate-tax   { background: #fee2e2; color: #b91c1c; }

    .empty-state {
        padding: 3rem; text-align: center; color: #94a3b8;
    }
    .empty-state svg { width: 3rem; height: 3rem; margin: 0 auto 0.75rem; display: block; opacity: 0.25; }
</style>
@endsection

@section('content')
<div class="deductions-page">

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h2 class="page-title">Government Contributions</h2>
            <p class="page-subtitle">SSS &nbsp;·&nbsp; PhilHealth &nbsp;·&nbsp; Pag-IBIG &nbsp;·&nbsp; BIR Withholding Tax</p>
        </div>
    </div>

    {{-- Date Filter --}}
    <form method="GET" action="{{ route('finance_admin.deductions.government') }}">
        <div class="filter-bar">
            <label>From</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}">
            <label>To</label>
            <input type="date" name="date_to" value="{{ $dateTo }}">
            <button type="submit" class="filter-btn">Apply Filter</button>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="summary-grid">
        <div class="summary-card" style="--card-color:#3b82f6;">
            <div class="summary-card-label">SSS Total</div>
            <div class="summary-card-value money">₱{{ number_format($totals['sss'], 2) }}</div>
        </div>
        <div class="summary-card" style="--card-color:#22c55e;">
            <div class="summary-card-label">PhilHealth Total</div>
            <div class="summary-card-value money">₱{{ number_format($totals['philhealth'], 2) }}</div>
        </div>
        <div class="summary-card" style="--card-color:#eab308;">
            <div class="summary-card-label">Pag-IBIG Total</div>
            <div class="summary-card-value money">₱{{ number_format($totals['hdmf'], 2) }}</div>
        </div>
        <div class="summary-card" style="--card-color:#ef4444;">
            <div class="summary-card-label">BIR Tax Total</div>
            <div class="summary-card-value money">₱{{ number_format($totals['tax'], 2) }}</div>
        </div>
        <div class="summary-card" style="--card-color:#1e293b;">
            <div class="summary-card-label">Grand Total</div>
            <div class="summary-card-value money">₱{{ number_format($totals['total_gov'], 2) }}</div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('breakdown', this)">Employee Breakdown</button>
        <button class="tab-btn" onclick="switchTab('sss', this)">SSS Rates</button>
        <button class="tab-btn" onclick="switchTab('philhealth', this)">PhilHealth Rates</button>
        <button class="tab-btn" onclick="switchTab('pagibig', this)">Pag-IBIG Rates</button>
        <button class="tab-btn" onclick="switchTab('tax', this)">BIR Tax Table</button>
    </div>

    {{-- Tab: Employee Breakdown --}}
    <div id="tab-breakdown" class="tab-panel active">
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">Per-Employee Contribution Breakdown</span>
                <span style="font-size:0.8rem;color:#64748b;">{{ $rows->count() }} record(s) found</span>
            </div>
            <div class="table-wrap">
                @if($rows->count())
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Period</th>
                            <th class="text-right">Gross Pay</th>
                            <th class="text-right"><span class="rate-badge rate-sss">SSS</span></th>
                            <th class="text-right"><span class="rate-badge rate-ph">PhilHealth</span></th>
                            <th class="text-right"><span class="rate-badge rate-hdmf">Pag-IBIG</span></th>
                            <th class="text-right"><span class="rate-badge rate-tax">BIR Tax</span></th>
                            <th class="text-right">Total Gov.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $i => $row)
                        <tr>
                            <td style="color:#94a3b8;">{{ $i + 1 }}</td>
                            <td style="font-weight:600;">{{ $row['employee'] }}</td>
                            <td>{{ $row['department'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($row['period_start'])->format('M d') }} – {{ \Carbon\Carbon::parse($row['period_end'])->format('M d, Y') }}</td>
                            <td class="text-right money">₱{{ number_format($row['gross_pay'], 2) }}</td>
                            <td class="text-right money">₱{{ number_format($row['sss'], 2) }}</td>
                            <td class="text-right money">₱{{ number_format($row['philhealth'], 2) }}</td>
                            <td class="text-right money">₱{{ number_format($row['hdmf'], 2) }}</td>
                            <td class="text-right money">₱{{ number_format($row['tax'], 2) }}</td>
                            <td class="text-right money" style="font-weight:700;">₱{{ number_format($row['total_gov'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="font-weight:700;">TOTALS</td>
                            <td class="text-right money tfoot">₱{{ number_format($rows->sum('gross_pay'), 2) }}</td>
                            <td class="text-right money tfoot">₱{{ number_format($totals['sss'], 2) }}</td>
                            <td class="text-right money tfoot">₱{{ number_format($totals['philhealth'], 2) }}</td>
                            <td class="text-right money tfoot">₱{{ number_format($totals['hdmf'], 2) }}</td>
                            <td class="text-right money tfoot">₱{{ number_format($totals['tax'], 2) }}</td>
                            <td class="text-right money tfoot">₱{{ number_format($totals['total_gov'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
                @else
                <div class="empty-state">
                    <i data-lucide="inbox" style="width:3rem;height:3rem;margin:0 auto 0.75rem;display:block;opacity:0.2;"></i>
                    No payroll records found for the selected period.
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tab: SSS Rates --}}
    <div id="tab-sss" class="tab-panel">
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">SSS Contribution Schedule</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Salary Range From</th>
                            <th>Salary Range To</th>
                            <th class="text-right">Monthly Salary Credit</th>
                            <th class="text-right">Employee Share</th>
                            <th class="text-right">Employer Share</th>
                            <th class="text-right">Total Contribution</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sssRates as $r)
                        <tr>
                            <td class="money">₱{{ number_format($r->sss_range_from, 2) }}</td>
                            <td class="money">₱{{ number_format($r->sss_range_to, 2) }}</td>
                            <td class="text-right money">₱{{ number_format($r->monthly_salary_credit, 2) }}</td>
                            <td class="text-right money">₱{{ number_format($r->employee_share, 2) }}</td>
                            <td class="text-right money">₱{{ number_format($r->employer_share, 2) }}</td>
                            <td class="text-right money" style="font-weight:700;">₱{{ number_format($r->total_contribution, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="empty-state">No SSS rate data available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tab: PhilHealth Rates --}}
    <div id="tab-philhealth" class="tab-panel">
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">PhilHealth Contribution Schedule</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Salary From</th>
                            <th>Salary To</th>
                            <th class="text-right">Contribution Rate (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($philhealthRates as $r)
                        <tr>
                            <td class="money">₱{{ number_format($r->salary_from, 2) }}</td>
                            <td class="money">₱{{ number_format($r->salary_to, 2) }}</td>
                            <td class="text-right money" style="font-weight:700;">{{ number_format($r->contribution_rate, 2) }}%</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="empty-state">No PhilHealth rate data available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tab: Pag-IBIG Rates --}}
    <div id="tab-pagibig" class="tab-panel">
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">Pag-IBIG (HDMF) Contribution Schedule</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Salary From</th>
                            <th>Salary To</th>
                            <th class="text-right">Employee Rate (%)</th>
                            <th class="text-right">Employer Rate (%)</th>
                            <th class="text-right">Max Contribution</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagibigRates as $r)
                        <tr>
                            <td class="money">₱{{ number_format($r->salary_from, 2) }}</td>
                            <td class="money">₱{{ number_format($r->salary_to, 2) }}</td>
                            <td class="text-right money">{{ number_format($r->employee_rate, 2) }}%</td>
                            <td class="text-right money">{{ number_format($r->employer_rate, 2) }}%</td>
                            <td class="text-right money" style="font-weight:700;">₱{{ number_format($r->maximum_contribution, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="empty-state">No Pag-IBIG rate data available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tab: BIR Tax Table --}}
    <div id="tab-tax" class="tab-panel">
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">BIR Withholding Tax Table</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Salary From</th>
                            <th>Salary To</th>
                            <th class="text-right">Base Tax</th>
                            <th class="text-right">Tax Rate (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($taxRates as $r)
                        <tr>
                            <td class="money">₱{{ number_format($r->salary_from, 2) }}</td>
                            <td class="money">₱{{ number_format($r->salary_to, 2) }}</td>
                            <td class="text-right money">₱{{ number_format($r->base_tax, 2) }}</td>
                            <td class="text-right money" style="font-weight:700;">{{ number_format($r->tax_rate, 2) }}%</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="empty-state">No tax rate data available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function switchTab(tabId, btn) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + tabId).classList.add('active');
        btn.classList.add('active');
    }
</script>
@endsection
