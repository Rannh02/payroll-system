@extends('layouts.master')

@section('title', 'Loan Deductions - VIA Architects Associates')

@section('styles')
<style>
    .deductions-page { max-width: 1200px; margin: 0 auto; }

    .page-header { display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
    .page-title { font-size: 1.75rem; font-weight: 800; color: #1e293b; margin: 0; }
    .page-subtitle { font-size: 0.875rem; color: #64748b; margin-top: 0.25rem; }

    .filter-bar {
        display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 1rem 1.25rem; margin-bottom: 2rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .filter-bar label { font-size: 0.8rem; font-weight: 600; color: #64748b; }
    .filter-bar input[type="date"] {
        border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.5rem 0.75rem;
        font-size: 0.875rem; color: #1e293b; background: #f8fafc;
    }
    .filter-btn {
        padding: 0.5rem 1.25rem; border-radius: 8px; border: none;
        background: #1e293b; color: #fff; font-size: 0.875rem; font-weight: 600;
        cursor: pointer; transition: background 0.2s;
    }
    .filter-btn:hover { background: #0f172a; }

    .summary-card-single {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        padding: 1.25rem 1.75rem; margin-bottom: 2rem; display: inline-flex;
        align-items: center; gap: 1.25rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04); border-top: 3px solid #f97316;
    }
    .summary-label { font-size: 0.8rem; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: 0.05em; }
    .summary-value { font-size: 1.75rem; font-weight: 800; color: #1e293b; }

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
        color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;
    }
    td {
        padding: 0.85rem 1rem; font-size: 0.875rem; color: #1e293b;
        border-bottom: 1px solid #f1f5f9; vertical-align: middle;
    }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #f8fafc; }
    .text-right { text-align: right; }
    .money { font-variant-numeric: tabular-nums; }
    .tfoot-row td { font-weight: 800; background: #f1f5f9; border-top: 2px solid #e2e8f0; }

    .loan-badge {
        display: inline-block; padding: 0.25rem 0.7rem; border-radius: 6px;
        font-size: 0.75rem; font-weight: 700;
        background: #fff7ed; color: #c2410c;
    }

    .empty-state {
        padding: 3rem; text-align: center; color: #94a3b8;
    }
</style>
@endsection

@section('content')
<div class="deductions-page">

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h2 class="page-title">Loan Deductions</h2>
            <p class="page-subtitle">Employee loan and cash advance deductions from payroll</p>
        </div>
    </div>

    {{-- Date Filter --}}
    <form method="GET" action="{{ route('finance_admin.deductions.loans') }}">
        <div class="filter-bar">
            <label>From</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}">
            <label>To</label>
            <input type="date" name="date_to" value="{{ $dateTo }}">
            <button type="submit" class="filter-btn">Apply Filter</button>
        </div>
    </form>

    {{-- Summary --}}
    <div class="summary-card-single">
        <div>
            <div class="summary-label">Total Loan Deductions</div>
            <div class="summary-value money">₱{{ number_format($totalAmount, 2) }}</div>
        </div>
        <div style="padding-left:1.5rem;border-left:1px solid #e2e8f0;">
            <div class="summary-label">Records Found</div>
            <div class="summary-value">{{ $rows->count() }}</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Loan Deduction Records</span>
            <span class="loan-badge">Loan / Cash Advance</span>
        </div>
        <div class="table-wrap">
            @if($rows->count())
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Pay Date</th>
                        <th>Period</th>
                        <th class="text-right">Loan Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $row)
                    <tr>
                        <td style="color:#94a3b8;">{{ $i + 1 }}</td>
                        <td style="font-weight:600;">{{ $row['employee'] }}</td>
                        <td>{{ $row['department'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($row['pay_date'])->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($row['period_start'])->format('M d') }} – {{ \Carbon\Carbon::parse($row['period_end'])->format('M d, Y') }}</td>
                        <td class="text-right money" style="color:#c2410c;font-weight:700;">₱{{ number_format($row['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="tfoot-row">
                        <td colspan="5" style="font-weight:700;">TOTAL</td>
                        <td class="text-right money">₱{{ number_format($totalAmount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            @else
            <div class="empty-state">
                <i data-lucide="credit-card" style="width:3rem;height:3rem;margin:0 auto 0.75rem;display:block;opacity:0.2;"></i>
                <p>No loan deductions found for the selected period.</p>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
