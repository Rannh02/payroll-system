@extends('layouts.master')

@section('title', 'Attendance Summary Report - VIA Architects Associates')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/user/style.css') }}">
    <style>
        .report-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3.5rem 2rem;
            color: #64748b;
            text-align: center;
            gap: 1rem;
        }
        .report-placeholder svg {
            opacity: 0.3;
            width: 56px;
            height: 56px;
        }
        .report-placeholder h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #94a3b8;
            margin: 0;
        }
        .report-placeholder p {
            font-size: 0.85rem;
            color: #475569;
            margin: 0;
            max-width: 320px;
            line-height: 1.5;
        }
        .results-fade-in {
            animation: fadeInUp 0.35s ease both;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
@endsection

@section('content')
    <div class="main-content-inner">

        <div class="content-header">
            <div>
                <h2 class="header-title">Attendance Summary Report</h2>
            </div>
        </div>

        @if(isset($error))
            <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <strong>Error:</strong> {{ $error }}
            </div>
        @endif

        <div class="report-card">
            <form method="GET" action="{{ route('user.attendance.report') }}">

                <div class="report-section">
                    <div class="date-filters">
                        <div class="filter-group">
                            <label class="filter-label" for="from_date">From:</label>
                            <input
                                type="date"
                                id="from_date"
                                name="from_date"
                                class="form-input"
                                value="{{ old('from_date', $from ?? '') }}"
                            >
                        </div>
                        <div class="filter-group">
                            <label class="filter-label" for="to_date">To:</label>
                            <input
                                type="date"
                                id="to_date"
                                name="to_date"
                                class="form-input"
                                value="{{ old('to_date', $to ?? '') }}"
                            >
                        </div>
                    </div>
                </div>

                <div class="report-divider"></div>
                <div class="report-section">
                    <div class="options-grid">
                        <div class="options-row">
                            <label class="checkbox-item">
                                <input
                                    type="checkbox"
                                    name="absences"
                                    value="1"
                                    {{ (old('absences', $showAbsences ?? true)) ? 'checked' : '' }}
                                >
                                <span class="checkbox-label">Absences</span>
                            </label>
                            <label class="checkbox-item">
                                <input
                                    type="checkbox"
                                    name="tardiness"
                                    value="1"
                                    {{ (old('tardiness', $showTardiness ?? true)) ? 'checked' : '' }}
                                >
                                <span class="checkbox-label">Tardiness</span>
                            </label>
                            <label class="checkbox-item">
                                <input
                                    type="checkbox"
                                    name="undertime"
                                    value="1"
                                    {{ (old('undertime', $showUndertime ?? true)) ? 'checked' : '' }}
                                >
                                <span class="checkbox-label">Undertime</span>
                            </label>
                            <label class="checkbox-item">
                                <input
                                    type="checkbox"
                                    name="unpaid_leave"
                                    value="1"
                                    {{ (old('unpaid_leave', $showUnpaidLeave ?? true)) ? 'checked' : '' }}
                                >
                                <span class="checkbox-label">Unpaid Leave</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="report-divider"></div>
                <div class="report-section">
                    <button type="submit" class="btn-generate">Generate Report</button>
                </div>
            </form>

            <div class="report-divider"></div>

            {{-- Only show results after the form has been submitted --}}
            @if(request()->has('from_date'))
                <div class="results-table-container results-fade-in">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Days Present</th>
                                @if($showAbsences ?? true)
                                    <th>Days Absent</th>
                                @endif
                                @if($showTardiness ?? true)
                                    <th>Late (min)</th>
                                @endif
                                @if($showUndertime ?? true)
                                    <th>Undertime (min)</th>
                                @endif
                                <th>Total Late / UTM</th>
                                <th>Total Hours</th>
                            </tr>
                        </thead>

                        <tbody>
                            @isset($present)
                                <tr class="summary-row">
                                    <td><strong>{{ Auth::user()->name }}</strong></td>
                                    <td>{{ $present }}</td>

                                    @if($showAbsences ?? true)
                                        <td>{{ $absences }}</td>
                                    @endif

                                    @if($showTardiness ?? true)
                                        <td>{{ $lateMinutes }}</td>
                                    @endif

                                    @if($showUndertime ?? true)
                                        <td>{{ $undertimeMinutes }}</td>
                                    @endif

                                    <td>{{ $totalLateUTM }}</td>
                                    <td>{{ $totalHours }}h</td>
                                </tr>
                            @endisset
                        </tbody>

                        @isset($attendances)
                            @if($attendances->isNotEmpty())
                                <tfoot>
                                    <tr>
                                        <th colspan="7" style="text-align:left; padding: 8px 12px;">
                                            Daily Breakdown
                                        </th>
                                    </tr>
                                    @foreach($attendances as $att)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($att->date)->format('M d, Y (D)') }}</td>
                                            <td>{{ $att->status }}</td>

                                            @if($showAbsences ?? true)
                                                <td>—</td>
                                            @endif

                                            @if($showTardiness ?? true)
                                                <td>{{ $att->late_minutes ?? 0 }}</td>
                                            @endif

                                            @if($showUndertime ?? true)
                                                <td>{{ $att->undertime_minutes ?? 0 }}</td>
                                            @endif

                                            <td>{{ ($att->late_minutes ?? 0) + ($att->undertime_minutes ?? 0) }}</td>
                                            <td>{{ round($att->total_hours ?? 0, 2) }}h</td>
                                        </tr>
                                    @endforeach
                                </tfoot>
                            @else
                                <tfoot>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 20px;">
                                            No attendance records found for the selected date range.
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        @endisset

                    </table>
                </div>
            @else
                {{-- Placeholder shown on first load before any search --}}
                <div class="report-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3>No Report Generated Yet</h3>
                    <p>Select a date range above and click <strong>Generate Report</strong> to view your attendance breakdown.</p>
                </div>
            @endif

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ── Validate: from_date must not be after to_date ──────────────
            const form     = document.querySelector('form[method="GET"]');
            const fromDate = document.getElementById('from_date');
            const toDate   = document.getElementById('to_date');

            if (form && fromDate && toDate) {
                form.addEventListener('submit', (e) => {
                    if (!fromDate.value || !toDate.value) {
                        e.preventDefault();
                        alert('Please select both a From and To date before generating the report.');
                        fromDate.focus();
                        return;
                    }
                    if (fromDate.value > toDate.value) {
                        e.preventDefault();
                        alert('The "From" date cannot be later than the "To" date.');
                        fromDate.focus();
                    }
                });

                // Auto-correct: if user picks a from_date after to_date, snap to_date
                fromDate.addEventListener('change', () => {
                    if (toDate.value && fromDate.value > toDate.value) {
                        toDate.value = fromDate.value;
                    }
                });
            }

            // ── Modal (kept from original, safely guarded) ────────────────
            const modal        = document.getElementById('attendanceModal');
            const closeModalBtn= document.getElementById('closeModal');

            if (!modal) return;

            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', () => {
                    modal.classList.remove('show');
                    document.body.style.overflow = '';
                });
            }

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });
        });
    </script>
@endsection