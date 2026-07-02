@extends('layouts.master')

@section('title', 'Attendance Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/common/modals.css') }}">
<link rel="stylesheet" href="{{ asset('css/common/tables.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/department.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('js/admin/attendance.js') }}"></script>
@endsection

@section('content')

<div class="govt-container">

    {{-- HEADER --}}
    <div class="content-header">
        <div>
            <h2 class="header-title">Attendance Management</h2>
            <p class="header-subtitle">
                Manage employee attendance records
            </p>
        </div>

        <button class="btn-primary" onclick="openModal()">
            <i data-lucide="plus"></i>
            Add Attendance
        </button>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i data-lucide="check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- ERROR MESSAGE --}}
    @if($errors->any())
        <div class="alert alert-error">
            <i data-lucide="alert-circle"></i>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- TABLE --}}
    <div class="department-table-container">

        <table class="department-table">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Total Hours</th>
                    <th>Overtime</th>
                    <th>Status</th>
                    <th>Late</th>
                    <th>Undertime</th>
                </tr>
            </thead>

            <tbody>

                @forelse($attendance as $index => $row)

                    <tr>

                        <td>{{ $index + 1 }}</td>

                        <td>
                            {{ $row->employee->first_name ?? '' }}
                            {{ $row->employee->last_name ?? '' }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}
                        </td>

                        <td>
                            {{ $row->time_in ?? '-' }}
                        </td>

                        <td>
                            {{ $row->time_out ?? '-' }}
                        </td>

                        <td>
                            {{ number_format($row->total_hours, 2) }}
                        </td>

                        <td>
                            {{ number_format($row->overtime_hours, 2) }}
                        </td>

                        <td>
                            <span class="badge-pg">
                                {{ $row->status }}
                            </span>
                        </td>

                        <td>
                            {{ $row->late_minutes }} mins
                        </td>

                        <td>
                            {{ $row->undertime_minutes }} mins
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="10" class="empty-state">
                            No attendance records found.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

{{-- ADD MODAL --}}
<div id="attendanceModal" class="modal-backdrop">

    <div class="modal-content">

        <div class="modal-header">

            <h3 class="modal-title">
                Add Attendance
            </h3>

            <button class="btn-close" onclick="closeModal()">
                <i data-lucide="x"></i>
            </button>

        </div>

        <form class="modal-form"
              action="{{ route('attendance.store') }}"
              method="POST">

            @csrf

            {{-- EMPLOYEE --}}
            <div class="form-group-full">

                <label>
                    Employee
                    <span class="required">*</span>
                </label>

                <select name="employee_id"
                        class="form-input"
                        required>

                    <option value="">
                        Select Employee
                    </option>

                    @foreach($employees as $employee)

                        <option value="{{ $employee->employee_id }}">

                            {{ $employee->first_name }}
                            {{ $employee->last_name }}

                        </option>

                    @endforeach

                </select>

            </div>

            {{-- DATE --}}
            <div class="form-row">

                <div class="form-group">

                    <label>
                        Date
                        <span class="required">*</span>
                    </label>

                    <input type="date"
                           name="date"
                           class="form-input"
                           required>

                </div>

                <div class="form-group">

                    <label>
                        Status
                        <span class="required">*</span>
                    </label>

                    <select name="status"
                            class="form-input"
                            required>

                        <option value="Present">
                            Present
                        </option>

                        <option value="Absent">
                            Absent
                        </option>

                        <option value="Leave">
                            Leave
                        </option>

                    </select>

                </div>

            </div>

            {{-- TIME --}}
            <div class="form-row">

                <div class="form-group">

                    <label>Time In</label>

                    <input type="time"
                           name="time_in"
                           class="form-input">

                </div>

                <div class="form-group">

                    <label>Time Out</label>

                    <input type="time"
                           name="time_out"
                           class="form-input">

                </div>

            </div>

            {{-- HOURS --}}
            <div class="form-row">

                <div class="form-group">

                    <label>Total Hours</label>

                    <input type="number"
                           step="0.01"
                           name="total_hours"
                           class="form-input"
                           value="0">

                </div>

                <div class="form-group">

                    <label>Overtime Hours</label>

                    <input type="number"
                           step="0.01"
                           name="overtime_hours"
                           class="form-input"
                           value="0">

                </div>

            </div>

            {{-- DEDUCTIONS --}}
            <div class="form-row">

                <div class="form-group">

                    <label>Late Minutes</label>

                    <input type="number"
                           name="late_minutes"
                           class="form-input"
                           value="0">

                </div>

                <div class="form-group">

                    <label>Undertime Minutes</label>

                    <input type="number"
                           name="undertime_minutes"
                           class="form-input"
                           value="0">

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">

                <button type="button"
                        class="btn-secondary"
                        onclick="closeModal()">

                    Cancel

                </button>

                <button type="submit"
                        class="btn-primary">

                    Save Attendance

                </button>

            </div>

        </form>

    </div>

</div>

@endsection
