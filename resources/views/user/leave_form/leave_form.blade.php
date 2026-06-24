@extends('layouts.master')

@section('title', 'Leave Form - VIA Architects Associates')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/user/leave_form.css') }}">
@endsection

@section('content')
    <div class="max-w-[1600px] mx-auto">
        <div class="content-header">
            <div>
                <h2 class="header-title">Leave Request Form</h2>
                <p class="header-subtitle">
                    <span class="subtitle-dot"></span>
                    Submit your leave request for approval.
                </p>
            </div>
        </div>

        <!-- Leave Form -->
        <div class="premium-card">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div
                    style="background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;padding:12px 16px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <i data-lucide="check-circle" class="h-4 w-4"></i> {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div
                    style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
                    <ul style="margin:0;padding-left:18px;">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('user.leave_form.store') }}" method="POST">
                @csrf

                <!-- Employee Information Section -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i data-lucide="user" class="h-5 w-5"></i>
                        Employee Details
                    </h3>

                    <div class="form-row-2">
                        <div class="premium-group">
                            <label for="employee_id" class="premium-label">Employee ID</label>
                            <input type="text" id="employee_id" name="employee_id"
                                value="{{ $employee->employee_id ?? 'N/A' }}" readonly class="premium-input">
                        </div>

                        <div class="premium-group">
                            <label for="department" class="premium-label">Department</label>
                            <input type="text" id="department" name="department"
                                value="{{ $employee->department->department_name ?? 'N/A' }}" readonly
                                class="premium-input">
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="premium-group">
                            <label for="employee_name" class="premium-label">Employee Name</label>
                            <input type="text" id="employee_name" name="employee_name"
                                value="{{ $employee ? trim($employee->first_name . ' ' . $employee->last_name) : 'N/A' }}"
                                readonly class="premium-input">
                        </div>

                        <div class="premium-group">
                            <label for="position" class="premium-label">Position</label>
                            <input type="text" id="position" name="position"
                                value="{{ $employee->position->position_name ?? 'N/A' }}" readonly class="premium-input">
                        </div>
                    </div>
                </div>

                <!-- Leave Details Section -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i data-lucide="calendar-plus" class="h-5 w-5"></i>
                        Leave Information
                    </h3>

                    <div class="form-row-2">
                        <div class="premium-group">
                            <label for="leave_type" class="premium-label">Leave Type</label>
                            <select id="leave_type" name="leave_type" class="premium-input" required>
                                <option value="" disabled selected>Select Leave Type</option>
                                <option value="vacation">Vacation</option>
                                <option value="sick">Sick Leave</option>
                                <option value="personal">Personal Leave</option>
                                <option value="family">Family Leave</option>
                                <option value="parental">Parental Leave</option>
                                <option value="bereavement">Bereavement Leave</option>
                                <option value="compensatory">Compensatory Leave</option>
                                <option value="vto">Volunteer Time Off (VTO)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="premium-group">
                            <label for="start_date" class="premium-label">Start Date</label>
                            <input type="date" id="start_date" name="start_date" class="premium-input" required>
                        </div>

                        <div class="premium-group">
                            <label for="end_date" class="premium-label">End Date</label>
                            <input type="date" id="end_date" name="end_date" class="premium-input" required>
                        </div>
                    </div>
                </div>

                <!-- Time Coding Section -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i data-lucide="tag" class="h-5 w-5"></i>
                        Code Time As
                    </h3>

                    <div class="toggles-grid">
                        <label class="custom-toggle">
                            <input type="checkbox" name="paid">
                            <div class="toggle-card">
                                <div class="toggle-icon"></div>
                                <span>Paid Leave</span>
                            </div>
                        </label>

                        <label class="custom-toggle">
                            <input type="checkbox" name="unpaid_leave">
                            <div class="toggle-card">
                                <div class="toggle-icon"></div>
                                <span>Unpaid Leave</span>
                            </div>
                        </label>

                        <label class="custom-toggle">
                            <input type="checkbox" name="undertime">
                            <div class="toggle-card">
                                <div class="toggle-icon"></div>
                                <span>Undertime</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reason" class="premium-label">Reason (Optional)</label>
                    <textarea id="reason" name="reason" class="premium-input" optional></textarea>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i data-lucide="send" class="h-5 w-5"></i>
                        Submit Leave Request
                    </button>
                </div>



                <!-- <div class="form-group">
                        <label for="attachment" class="premium-label">Attachment</label>
                        <input type="file" id="attachment" name="attachment" class="premium-input">
                    </div> -->
            </form>
        </div>

    </div>
    </div>
@endsection