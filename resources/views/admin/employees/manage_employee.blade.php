@extends('layouts.master')

@section('title', 'Manage Employees - VIA Architects Associates')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/manage-employee.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/employee-form.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('js/admin/employees/manage_employee.js') }}"></script>
@endsection

@section('content')
    @php
        $oldEdit = $errors->any() && old('db_id') ? [
            'db_id' => old('db_id'),
            'employee_number' => old('employee_id'),
            'first_name' => old('first_name'),
            'middle_name' => old('middle_name'),
            'last_name' => old('last_name'),
            'suffix' => old('suffix'),
            'sex' => old('sex'),
            'date_of_birth' => old('date_of_birth'),
            'marital_status' => old('marital_status'),
            'number_of_dependents' => old('dependents'),
            'contact_info' => old('phone'),
            'current_street_address' => old('current_street_address'),
            'current_barangay' => old('current_barangay'),
            'current_city_municipality' => old('current_city'),
            'current_province' => old('current_province'),
            'current_zip_code' => old('current_zip_code'),
            'permanent_street_address' => old('permanent_street_address'),
            'permanent_barangay' => old('permanent_barangay'),
            'permanent_city_municipality' => old('permanent_city'),
            'permanent_province' => old('permanent_province'),
            'permanent_zip_code' => old('permanent_zip_code'),
            'sss_num' => old('sss_num'),
            'philhealth_num' => old('philhealth_num'),
            'pagibig_num' => old('pagibig_num'),
            'email' => old('email'),
            'photo_url' => old('photo_url'),
            'has_custom_photo' => old('has_custom_photo') == '1',
            'hire_date' => old('join_date'),
            'employment_status' => old('employee_status'),
            'department_id' => old('department'),
            'position_id' => old('position'),
            'salary_rate' => old('salary'),
        ] : null;
    @endphp

    <div class="max-w-6xl mx-auto" id="manage-employee-root" data-old-edit="{{ $oldEdit ? json_encode($oldEdit) : '' }}">
        <div class="content-header">
            <div>
                <h2 class="header-title">Manage Employees</h2>
                <p class="header-subtitle">
                    <span class="subtitle-dot"></span>
                    Search, filter, and manage your employee records.
                </p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="{{ route('employees.archived') }}" class="btn-secondary" title="View archived employees">
                    <i data-lucide="archive" class="h-4 w-4"></i>
                    View Archives
                </a>
                <a href="{{ route('employees.create') }}" class="btn-primary" title="Add a new employee" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; cursor: pointer;">
                    <i data-lucide="user-plus" class="h-4 w-4"></i>
                    Add New Employee
                </a>
            </div>
        </div>

        <div class="employee-search-container">
            <div class="employee-search-input-wrapper">
                <input type="text" placeholder="Search employees..." class="employee-search-input" />
            </div>
            <div class="employee-department-select">
                <select>
                    <option>All Departments</option>
                    <option>Engineering</option>
                    <option>Finance</option>
                    <option>Sales</option>
                    <option>Marketing</option>
                </select>
            </div>
        </div>

        @if(session('success'))
            <div style="background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;padding:12px 16px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                <i data-lucide="check-circle" class="h-4 w-4"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background:#fee2e2; border: 1px solid #fecaca; color:#991b1b; padding:12px 16px; border-radius: 8px; margin-bottom: 16px;">
                <p style="font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="alert-circle" class="h-4 w-4"></i> Please check the following errors:
                </p>
                <ul style="font-size: 0.8125rem; list-style: inside; padding-left: 4px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="employee-table-container">
            <table class="employee-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Salary</th>
                        <th>Allowance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td class="employee-id">{{ $employee->employee_number ?? 'E' . str_pad($employee->employee_id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="employee-name-cell">
                                    <img src="{{ $employee->photo_url }}" alt="" class="employee-avatar" style="object-fit: cover;">
                                    <div>
                                        <p class="employee-name">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                                        <p class="employee-since">Since {{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('Y-m-d') : 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="employee-position">{{ $employee->position->position_name ?? 'N/A' }}</td>
                            <td>
                                <span class="department-badge badge-{{ strtolower(str_replace(' ', '-', $employee->department->department_name ?? 'none')) }}">
                                    {{ $employee->department->department_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="employee-salary">&#8369;{{ number_format($employee->salary_rate ?? $employee->position->basic_salary ?? 0, 2) }}</td>
                            <td class="employee-allowance">&#8369;0.00</td>
                            <td>
                                <span class="status-badge badge-{{ strtolower($employee->employment_status ?? 'regular') }}">
                                    {{ $employee->employment_status ?? 'Regular' }}
                                </span>
                            </td>
                            <td>
                                <div class="employee-action-group">
                                    <button type="button"
                                        class="employee-action-link action-view"
                                        data-employee="{{ json_encode([
                                            "db_id" => $employee->employee_id,
                                            "id" => $employee->employee_number ?? ("E" . str_pad($employee->employee_id, 3, "0", STR_PAD_LEFT)),
                                            "name" => trim($employee->first_name . " " . $employee->last_name),
                                            "photo_url" => $employee->photo_url,
                                            "email" => $employee->user->email ?? "N/A",
                                            "phone" => $employee->contact_info ?? "N/A",
                                            "position" => $employee->position->position_name ?? "N/A",
                                            "department" => $employee->department->department_name ?? "N/A",
                                            "salary" => "&#8369;" . number_format($employee->salary_rate ?? $employee->position->basic_salary ?? 0, 2),
                                            "status" => $employee->employment_status ?? "Regular",
                                            "hire-date" => $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format("M d, Y") : "N/A",
                                            "sex" => $employee->sex ?? "N/A",
                                            "birth" => $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format("M d, Y") : "N/A",
                                            "address" => trim(($employee->current_street_address ?? "") . " " . ($employee->current_barangay ?? "") . " " . ($employee->current_city_municipality ?? "")),
                                            "sss" => $employee->sss_num ?? "N/A",
                                            "philhealth" => $employee->philhealth_num ?? "N/A",
                                            "pagibig" => $employee->pagibig_num ?? "N/A"
                                        ]) }}"
                                        onclick="openViewModal(JSON.parse(this.dataset.employee))">
                                        View
                                    </button>
                                    <button type="button"
                                        class="employee-action-link action-edit"
                                        data-employee="{{ json_encode([
                                            "db_id" => $employee->employee_id,
                                            "employee_number" => $employee->employee_number ?? ("E" . str_pad($employee->employee_id, 3, "0", STR_PAD_LEFT)),
                                            "first_name" => $employee->first_name,
                                            "middle_name" => $employee->middle_name,
                                            "last_name" => $employee->last_name,
                                            "suffix" => $employee->suffix,
                                            "sex" => $employee->sex,
                                            "date_of_birth" => $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format("Y-m-d") : "",
                                            "marital_status" => $employee->marital_status,
                                            "number_of_dependents" => $employee->number_of_dependents,
                                            "contact_info" => $employee->contact_info,
                                            "current_street_address" => $employee->current_street_address,
                                            "current_barangay" => $employee->current_barangay,
                                            "current_city_municipality" => $employee->current_city_municipality,
                                            "current_province" => $employee->current_province,
                                            "current_zip_code" => $employee->current_zip_code,
                                            "permanent_street_address" => $employee->permanent_street_address,
                                            "permanent_barangay" => $employee->permanent_barangay,
                                            "permanent_city_municipality" => $employee->permanent_city_municipality,
                                            "permanent_province" => $employee->permanent_province,
                                            "permanent_zip_code" => $employee->permanent_zip_code,
                                            "sss_num" => $employee->sss_num,
                                            "philhealth_num" => $employee->philhealth_num,
                                            "pagibig_num" => $employee->pagibig_num,
                                            "email" => $employee->user->email ?? "",
                                            "photo_url" => $employee->photo_url,
                                            "has_custom_photo" => !empty($employee->profile_photo),
                                            "hire_date" => $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format("Y-m-d") : "",
                                            "employment_status" => $employee->employment_status,
                                            "department_id" => $employee->department_id,
                                            "position_id" => $employee->position_id,
                                            "salary_rate" => $employee->salary_rate ?? ($employee->position->basic_salary ?? "")
                                        ]) }}"
                                        onclick="openEditModal(JSON.parse(this.dataset.employee))">
                                        Edit
                                    </button>
                                    <button type="button"
                                        class="employee-action-link action-archive"
                                        onclick="openArchiveModal('{{ $employee->employee_id }}', '{{ $employee->first_name }} {{ $employee->last_name }}')">
                                        Archive
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; padding: 40px; color: #64748b;">
                                <div style="display:flex; flex-direction:column; align-items:center; gap:1rem;">
                                    <i data-lucide="users" class="h-12 w-12 opacity-20"></i>
                                    <p>No active employees found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="archive-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-inner">
                <div class="modal-top">
                    <div class="modal-icon-container">
                        <i data-lucide="archive" class="h-6 w-6"></i>
                    </div>
                    <div class="modal-info">
                        <h3 class="modal-title">Archive Employee</h3>
                        <p class="modal-description">
                            Are you sure you want to archive <strong id="employee-name-display" style="color: inherit; font-weight: 700;"></strong>?
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal btn-modal-secondary" onclick="closeArchiveModal()">Cancel</button>
                <form id="archive-form" method="POST" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-modal btn-modal-danger">Archive Employee</button>
                </form>
            </div>
        </div>
    </div>

    <div id="view-modal" class="modal-overlay">
        <div class="modal-content modal-content-view">
            <div class="view-modal-header">
                <div class="view-header-avatar-container">
                    <img id="view-header-avatar-img" src="" alt="" class="view-header-avatar hidden" style="object-fit: cover;">
                    <div class="view-header-avatar" id="view-header-avatar-initial">?</div>
                </div>
                <div class="view-header-info">
                    <h2 class="view-header-name" id="view-header-name">Employee Name</h2>
                    <div class="view-header-title">
                        <i data-lucide="briefcase" class="h-4 w-4"></i>
                        <span id="view-header-position">Position Title</span>
                        <span style="opacity: 0.3;">•</span>
                        <span id="view-header-id">ID: ---</span>
                    </div>
                </div>
                <button type="button" class="view-modal-close-btn" onclick="closeViewModal()">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <div class="view-modal-body">
                <div class="view-section-grid">
                    <div class="section-divider"><span>Personal Information</span><div class="section-line"></div></div>
                    <div class="view-card"><div class="view-card-icon"><i data-lucide="user"></i></div><div class="view-card-content"><p class="view-card-label">Full Name</p><p class="view-card-value" id="view-name">---</p></div></div>
                    <div class="view-card"><div class="view-card-icon"><i data-lucide="mail"></i></div><div class="view-card-content"><p class="view-card-label">Email Address</p><p class="view-card-value" id="view-email">---</p></div></div>
                    <div class="view-card"><div class="view-card-icon"><i data-lucide="phone"></i></div><div class="view-card-content"><p class="view-card-label">Phone Number</p><p class="view-card-value" id="view-phone">---</p></div></div>
                    <div class="view-card"><div class="view-card-icon"><i data-lucide="users"></i></div><div class="view-card-content"><p class="view-card-label">Sex</p><p class="view-card-value" id="view-sex">---</p></div></div>
                    <div class="view-card"><div class="view-card-icon"><i data-lucide="calendar"></i></div><div class="view-card-content"><p class="view-card-label">Date of Birth</p><p class="view-card-value" id="view-birth">---</p></div></div>
                    <div class="view-card"><div class="view-card-icon"><i data-lucide="map-pin"></i></div><div class="view-card-content"><p class="view-card-label">Address</p><p class="view-card-value" id="view-address">---</p></div></div>

                    <div class="section-divider"><span>Employment Details</span><div class="section-line"></div></div>
                    <div class="view-card"><div class="view-card-icon"><i data-lucide="award"></i></div><div class="view-card-content"><p class="view-card-label">Department</p><p class="view-card-value" id="view-department">---</p></div></div>
                    <div class="view-card"><div class="view-card-icon"><i data-lucide="dollar-sign"></i></div><div class="view-card-content"><p class="view-card-label">Monthly Salary</p><p class="view-card-value" id="view-salary">---</p></div></div>
                    <div class="view-card"><div class="view-card-icon"><i data-lucide="shield-check"></i></div><div class="view-card-content"><p class="view-card-label">Employment Status</p><p class="view-card-value" id="view-status">---</p></div></div>
                    <div class="view-card"><div class="view-card-icon"><i data-lucide="calendar-check"></i></div><div class="view-card-content"><p class="view-card-label">Hire Date</p><p class="view-card-value" id="view-hire-date">---</p></div></div>

                    <div class="section-divider"><span>Government Identifiers</span><div class="section-line"></div></div>
                    <div class="view-card"><div class="view-card-icon"><i data-lucide="fingerprint"></i></div><div class="view-card-content"><p class="view-card-label">SSS Number</p><p class="view-card-value" id="view-sss">---</p></div></div>
                    <div class="view-card"><div class="view-card-icon"><i data-lucide="heart"></i></div><div class="view-card-content"><p class="view-card-label">PhilHealth</p><p class="view-card-value" id="view-philhealth">---</p></div></div>
                    <div class="view-card"><div class="view-card-icon"><i data-lucide="home"></i></div><div class="view-card-content"><p class="view-card-label">Pag-IBIG</p><p class="view-card-value" id="view-pagibig">---</p></div></div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 1.25rem 1.5rem;">
                <button type="button" class="btn-modal btn-modal-secondary px-8" onclick="closeViewModal()" style="width: 100%;">Close Profile</button>
            </div>
        </div>
    </div>

    <div id="edit-modal" class="modal-overlay">
        <div class="modal-content modal-content-edit" style="max-width: 950px; border: none;">
            <div class="view-modal-header" style="padding: 1.5rem 2rem;">
                <div class="view-header-info">
                    <h2 class="view-header-name">Edit Employee Record</h2>
                    <div class="view-header-title">
                        <i data-lucide="user" class="h-4 w-4"></i>
                        <span>Modify employee details and credentials.</span>
                    </div>
                </div>
                <button type="button" class="view-modal-close-btn" onclick="closeEditModal()">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <form id="edit-form" method="POST" enctype="multipart/form-data" class="employee-form" style="margin: 0;">
                @csrf
                @method('PUT')
                <input type="hidden" name="db_id" id="edit_db_id">
                <input type="hidden" name="photo_url" id="edit_photo_url_hidden">
                <input type="hidden" name="has_custom_photo" id="edit_has_custom_photo_hidden">

                <div class="view-modal-body" style="max-height: 70vh; overflow-y: auto; padding: 2rem;">
                    <div style="display: flex; flex-direction: column; gap: 2rem;">
                        <div class="form-card">
                            <div class="form-section-header">
                                <i data-lucide="briefcase" class="h-5 w-5 text-teal-400"></i>
                                <h3>Employment Information</h3>
                            </div>
                            <div class="form-group-stack">
                                <div class="form-row-3">
                                    <div class="form-group"><label class="form-label">Employee ID:</label><input type="text" id="edit_employee_id" name="employee_id" class="form-input readonly-field" readonly></div>
                                    <div class="form-group"><label class="form-label">Date Joined:</label><input type="date" id="edit_join_date" name="join_date" class="form-input"></div>
                                    <div class="form-group">
                                        <label class="form-label">Employment Status:</label>
                                        <select id="edit_employee_status" name="employee_status" class="form-select">
                                            <option value="" disabled>Select Status</option>
                                            <option value="Regular">Regular</option>
                                            <option value="Probationary">Probationary</option>
                                            <option value="Contractual">Contractual</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row-3">
                                    <div class="form-group">
                                        <label class="form-label">Department:</label>
                                        <select id="edit_department" name="department" class="form-select">
                                            <option value="" disabled>Select Department</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->department_id }}">{{ $dept->department_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Position:</label>
                                        <select id="edit_position_select" name="position" class="form-select">
                                            <option value="" disabled>Select Position</option>
                                            @foreach($positions as $pos)
                                                <option value="{{ $pos->position_id }}" data-salary="{{ $pos->basic_salary }}">{{ $pos->position_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group"><label class="form-label">Basic Salary:</label><input type="number" id="edit_salary_input" name="salary" class="form-input readonly-field" readonly></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-card">
                            <div class="form-section-header">
                                <i data-lucide="user" class="h-5 w-5 text-teal-400"></i>
                                <h3>Personal Information</h3>
                            </div>
                            <div class="form-group-stack">
                                <div class="form-row-4">
                                    <div class="form-group"><label class="form-label">First Name:</label><input type="text" id="edit_first_name" name="first_name" class="form-input" placeholder="First Name"></div>
                                    <div class="form-group"><label class="form-label">Middle Name (Optional):</label><input type="text" id="edit_middle_name" name="middle_name" class="form-input" placeholder="Middle Name"></div>
                                    <div class="form-group"><label class="form-label">Last Name:</label><input type="text" id="edit_last_name" name="last_name" class="form-input" placeholder="Last Name"></div>
                                    <div class="form-group"><label class="form-label">Suffix:</label><input type="text" id="edit_suffix" name="suffix" class="form-input" placeholder="e.g. Jr."></div>
                                </div>
                                <div class="form-row-4">
                                    <div class="form-group"><label class="form-label">Date of Birth:</label><input type="date" id="edit_date_of_birth" name="date_of_birth" class="form-input"></div>
                                    <div class="form-group">
                                        <label class="form-label">Sex:</label>
                                        <select id="edit_sex" name="sex" class="form-select">
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Marital Status:</label>
                                        <select id="edit_marital_status" name="marital_status" class="form-select">
                                            <option value="" disabled>Select</option>
                                            <option value="Single">Single</option>
                                            <option value="Married">Married</option>
                                            <option value="Widowed">Widowed</option>
                                        </select>
                                    </div>
                                    <div class="form-group"><label class="form-label">Dependents:</label><input type="number" id="edit_dependents" name="dependents" class="form-input" placeholder="0"></div>
                                </div>
                                <div class="form-row-2">
                                    <div class="form-group"><label class="form-label">Contact Number:</label><input type="text" id="edit_phone_input" name="phone" class="form-input" placeholder="+63 9XX XXX XXXX" maxlength="16"></div>
                                    <div></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-card">
                            <div class="form-section-header">
                                <i data-lucide="map-pin" class="h-5 w-5 text-teal-400"></i>
                                <h3>Residential Information</h3>
                            </div>
                            <div class="form-group-stack">
                                <h4 style="font-size: 0.75rem; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 1.25rem; margin-top: 0.5rem;">Current Residence</h4>
                                <div class="form-group" style="margin-bottom: 1rem;"><label class="form-label">Street Address:</label><input type="text" id="edit_current_street_address" name="current_street_address" class="form-input" placeholder="Unit, House No., Street, Subdivision"></div>
                                <div class="form-row-4">
                                    <div class="form-group"><label class="form-label">Barangay:</label><input type="text" id="edit_current_barangay" name="current_barangay" class="form-input"></div>
                                    <div class="form-group"><label class="form-label">City/Municipality:</label><input type="text" id="edit_current_city" name="current_city" class="form-input"></div>
                                    <div class="form-group"><label class="form-label">Province:</label><input type="text" id="edit_current_province" name="current_province" class="form-input"></div>
                                    <div class="form-group"><label class="form-label">Postal Code:</label><input type="text" id="edit_current_zip_code" name="current_zip_code" class="form-input" maxlength="10"></div>
                                </div>
                                <div style="margin-top: 2.5rem; border-top: 1px solid var(--glass-border); padding-top: 2.5rem;">
                                    <h4 style="font-size: 0.8125rem; font-weight: 800; color: var(--slate-400); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1.25rem;">Permanent Address</h4>
                                    <div class="form-group" style="margin-bottom: 1rem;"><label class="form-label">Street Address:</label><input type="text" id="edit_permanent_street_address" name="permanent_street_address" class="form-input"></div>
                                    <div class="form-row-4">
                                        <div class="form-group"><label class="form-label">Barangay:</label><input type="text" id="edit_permanent_barangay" name="permanent_barangay" class="form-input"></div>
                                        <div class="form-group"><label class="form-label">City/Municipality:</label><input type="text" id="edit_permanent_city" name="permanent_city" class="form-input"></div>
                                        <div class="form-group"><label class="form-label">Province:</label><input type="text" id="edit_permanent_province" name="permanent_province" class="form-input"></div>
                                        <div class="form-group"><label class="form-label">Postal Code:</label><input type="text" id="edit_permanent_zip_code" name="permanent_zip_code" class="form-input" maxlength="10"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-card" style="margin-top: 3rem; margin-bottom: 2.5rem;">
                            <div class="form-section-header">
                                <i data-lucide="shield-check" class="h-5 w-5 text-teal-400"></i>
                                <h3>Government Identifiers</h3>
                            </div>
                            <div class="form-group-stack">
                                <div class="form-row-3">
                                    <div class="form-group"><label class="form-label">SSS Number:</label><input type="text" id="edit_sss_input" name="sss_num" class="form-input" placeholder="00-0000000-0" maxlength="12"></div>
                                    <div class="form-group"><label class="form-label">PhilHealth Number:</label><input type="text" id="edit_philhealth_input" name="philhealth_num" class="form-input" placeholder="00-000000000-0" maxlength="14"></div>
                                    <div class="form-group"><label class="form-label">Pag-IBIG Number:</label><input type="text" id="edit_pagibig_input" name="pagibig_num" class="form-input" placeholder="0000-0000-0000" maxlength="14"></div>
                                </div>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 200px 1fr; gap: 2rem;">
                            <div class="form-card">
                                <div class="form-section-header" style="margin-bottom: 1.5rem;"><i data-lucide="camera" class="h-5 w-5 text-teal-400"></i><h3>Photo</h3></div>
                                <div style="display:flex; flex-direction:column; align-items:center; gap:1rem;">
                                    <div class="avatar-placeholder" id="edit_avatar_preview_container" style="width: 100px; height: 100px; border-radius: 1rem;">
                                        <i data-lucide="user" class="h-10 w-10 text-slate-700" id="edit_avatar_icon"></i>
                                        <img id="edit_avatar_preview" class="h-full w-full object-cover rounded-xl" style="display:none;" src="" alt="Preview">
                                    </div>
                                    <input type="file" name="profile_photo" id="edit_profile_photo_input" accept="image/*" style="display:none;">
                                    <button type="button" class="btn-secondary w-full" id="edit_upload_photo_btn" style="font-size:0.75rem; padding:0.5rem;">Upload Photo</button>
                                </div>
                            </div>
                            <div class="form-card">
                                <div class="form-section-header"><i data-lucide="lock" class="h-5 w-5 text-teal-400"></i><h3>Account Security</h3></div>
                                <div class="form-group-stack">
                                    <div class="form-group" style="margin-bottom:1.25rem;"><label class="form-label">Work Email Address:</label><input type="email" id="edit_email" name="email" class="form-input" placeholder="name@via-architects.com"></div>
                                    <div class="form-row-2">
                                        <div class="form-group"><label class="form-label">New Password:</label><input type="password" name="password" class="form-input" placeholder="•••••••• (leave blank to keep current)"></div>
                                        <div class="form-group"><label class="form-label">Confirm Password:</label><input type="password" name="password_confirmation" class="form-input" placeholder="•••••••• (leave blank to keep current)"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="padding: 1.5rem 2rem;">
                    <button type="button" class="btn-modal btn-modal-secondary" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-primary" style="background: linear-gradient(135deg, var(--primary) 0%, #2563eb 100%); border: none; box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4); color: white;">
                        <i data-lucide="save" class="h-4 w-4"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection