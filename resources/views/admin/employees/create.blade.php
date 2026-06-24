@extends('layouts.master')

@section('title', 'Create Employee - VIA Architects Associates')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/employee-form.css') }}">
@endsection

@section('content')
    <div class="max-w-5xl mx-auto">
        {{-- Page Header --}}
        <div class="content-header" style="margin-bottom: 2.5rem;">
            <div>
                <h2 class="header-title"
                    style="font-size: 2rem; font-weight: 850; letter-spacing: -0.03em; color: var(--text-main);">Create
                    Employee Record</h2>
                <p class="header-subtitle" style="color: var(--slate-400); font-size: 0.9375rem; font-weight: 500;">
                    <span class="subtitle-dot"></span>
                    Onboard a new professional to the VIA Architects Associates system.
                </p>
            </div>
        </div>

        @if($errors->any())
            <div
                style="background:#fee2e2; border: 1px solid #fecaca; color:#991b1b; padding:1rem; border-radius: 0.75rem; margin-bottom: 2rem;">
                <p style="font-weight: 700; margin-bottom: 0.5rem;">Please check the following errors:</p>
                <ul style="font-size: 0.8125rem; list-style: inside;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($errors->has('duplicate'))
            <script>
        document.addEventListener('DOMContentLoaded', function () {
            alert("{{ $errors->first('duplicate') }}");
        });
            </script>
        @endif
        <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" class="employee-form">
            @csrf

            <div style="display: flex; flex-direction: column; gap: 2rem;">

                {{-- SECTION 1: EMPLOYMENT IDENTIFICATION --}}
                <div class="form-card">
                    <div class="form-section-header">
                        <i data-lucide="briefcase" class="h-5 w-5 text-teal-400"></i>
                        <h3>Employment Information</h3>
                    </div>
                    <div class="form-group-stack">
                        <div class="form-row-3">
                            <div class="form-group">
                                <label class="form-label">Employee ID:</label>
                                <input type="text" name="employee_id" class="form-input readonly-field"
                                    value="{{ $nextEmployeeId }}" readonly>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date Joined:</label>
                                <input type="date" name="join_date" class="form-input" value="{{ old('join_date') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Employment Status:</label>
                                <select name="employee_status" class="form-select">
                                    <option value="" disabled selected>Select Status</option>
                                    <option value="Regular" {{ old('employee_status') === 'Regular' ? 'selected' : '' }}>
                                        Regular</option>
                                    <option value="Probationary" {{ old('employee_status') === 'Probationary' ? 'selected' : '' }}>Probationary</option>
                                    <option value="Contractual" {{ old('employee_status') === 'Contractual' ? 'selected' : '' }}>Contractual</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row-3">
                            <div class="form-group">
                                <label class="form-label">Department:</label>
                                <select name="department" class="form-select">
                                    <option value="" disabled selected>Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->department_id }}" {{ old('department') == $dept->department_id ? 'selected' : '' }}>{{ $dept->department_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Position:</label>
                                <select id="position_select" name="position" class="form-select">
                                    <option value="" disabled selected>Select Position</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->position_id }}" data-salary="{{ $pos->basic_salary }}" {{ old('position') == $pos->position_id ? 'selected' : '' }}>
                                            {{ $pos->position_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Basic Salary:</label>
                                <input type="number" id="salary_input" name="salary" class="form-input readonly-field"
                                    placeholder="Auto-filled" value="{{ old('salary') }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: PERSONAL INFORMATION --}}
                <div class="form-card">
                    <div class="form-section-header">
                        <i data-lucide="user" class="h-5 w-5 text-teal-400"></i>
                        <h3>Personal Information</h3>
                    </div>
                    <div class="form-group-stack">
                        <div class="form-row-4">
                            <div class="form-group">
                                <label class="form-label">First Name:</label>
                                <input type="text" name="first_name" class="form-input" placeholder="First Name"
                                    value="{{ old('first_name') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Middle Name (Optional):</label>
                                <input type="text" name="middle_name" class="form-input" placeholder="Middle Name"
                                    value="{{ old('middle_name') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last Name:</label>
                                <input type="text" name="last_name" class="form-input" placeholder="Last Name"
                                    value="{{ old('last_name') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Suffix:</label>
                                <input type="text" name="suffix" class="form-input" placeholder="e.g. Jr."
                                    value="{{ old('suffix') }}">
                            </div>
                        </div>

                        <div class="form-row-4">
                            <div class="form-group">
                                <label class="form-label">Date of Birth:</label>
                                <input type="date" name="date_of_birth" class="form-input"
                                    value="{{ old('date_of_birth') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Sex:</label>
                                <select name="sex" class="form-select">
                                    <option value="Male" {{ old('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Marital Status:</label>
                                <select name="marital_status" class="form-select">
                                    <option value="" disabled selected>Select</option>
                                    <option value="Single" {{ old('marital_status') === 'Single' ? 'selected' : '' }}>Single
                                    </option>
                                    <option value="Married" {{ old('marital_status') === 'Married' ? 'selected' : '' }}>
                                        Married</option>
                                    <option value="Widowed" {{ old('marital_status') === 'Widowed' ? 'selected' : '' }}>
                                        Widowed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Dependents:</label>
                                <input type="number" name="dependents" class="form-input" placeholder="0"
                                    value="{{ old('dependents') }}">
                            </div>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label">Contact Number:</label>
                                <input type="text" id="phone_input" name="phone" class="form-input"
                                    placeholder="+63 9XX XXX XXXX" value="{{ old('phone', '+63 9') }}" maxlength="16">
                            </div>
                            <div></div> {{-- Empty space to prevent stretching --}}
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: RESIDENTIAL INFORMATION --}}
                <div class="form-card">
                    <div class="form-section-header">
                        <i data-lucide="map-pin" class="h-5 w-5 text-teal-400"></i>
                        <h3>Residential Information</h3>
                    </div>
                    <div class="form-group-stack">
                        <h4
                            style="font-size: 0.75rem; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 1.25rem; margin-top: 0.5rem;">
                            Current Residence</h4>
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Street Address:</label>
                            <input type="text" name="current_street_address" class="form-input"
                                placeholder="Unit, House No., Street, Subdivision"
                                value="{{ old('current_street_address') }}">
                        </div>
                        <div class="form-row-4">
                            <div class="form-group">
                                <label class="form-label">Barangay:</label>
                                <input type="text" name="current_barangay" class="form-input"
                                    value="{{ old('current_barangay') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">City/Municipality:</label>
                                <input type="text" name="current_city" class="form-input" value="{{ old('current_city') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Province:</label>
                                <input type="text" name="current_province" class="form-input"
                                    value="{{ old('current_province') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Postal Code:</label>
                                <input type="text" name="current_zip_code" class="form-input"
                                    value="{{ old('current_zip_code') }}" maxlength="10">
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 2.5rem; border-top: 1px solid var(--glass-border); padding-top: 2.5rem;">
                        <h4
                            style="font-size: 0.8125rem; font-weight: 800; color: var(--slate-400); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1.25rem;">
                            Permanent Address</h4>
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Street Address:</label>
                            <input type="text" name="permanent_street_address" class="form-input"
                                value="{{ old('permanent_street_address') }}">
                        </div>
                        <div class="form-row-4">
                            <div class="form-group">
                                <label class="form-label">Barangay:</label>
                                <input type="text" name="permanent_barangay" class="form-input"
                                    value="{{ old('permanent_barangay') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">City/Municipality:</label>
                                <input type="text" name="permanent_city" class="form-input"
                                    value="{{ old('permanent_city') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Province:</label>
                                <input type="text" name="permanent_province" class="form-input"
                                    value="{{ old('permanent_province') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Postal Code:</label>
                                <input type="text" name="permanent_zip_code" class="form-input"
                                    value="{{ old('permanent_zip_code') }}" maxlength="10">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 4: GOVERNMENT IDS --}}
            <div class="form-card" style="margin-top: 3rem; margin-bottom: 2.5rem;">
                <div class="form-section-header">
                    <i data-lucide="shield-check" class="h-5 w-5 text-teal-400"></i>
                    <h3>Government Identifiers</h3>
                </div>
                <div class="form-group-stack">
                    <div class="form-row-3">
                        <div class="form-group">
                            <label class="form-label">SSS Number:</label>
                            <input type="text" id="sss_input" name="sss_num" class="form-input" placeholder="00-0000000-0"
                                value="{{ old('sss_num') }}" maxlength="12">
                        </div>
                        <div class="form-group">
                            <label class="form-label">PhilHealth Number:</label>
                            <input type="text" id="philhealth_input" name="philhealth_num" class="form-input"
                                placeholder="00-000000000-0" value="{{ old('philhealth_num') }}" maxlength="14">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pag-IBIG Number:</label>
                            <input type="text" id="pagibig_input" name="pagibig_num" class="form-input"
                                placeholder="0000-0000-0000" value="{{ old('pagibig_num') }}" maxlength="14">
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 5: PHOTO & SECURITY --}}
            <div style="display: grid; grid-template-columns: 200px 1fr; gap: 2rem;">
                <div class="form-card">
                    <div class="form-section-header" style="margin-bottom: 1.5rem;">
                        <i data-lucide="camera" class="h-5 w-5 text-teal-400"></i>
                        <h3>Photo</h3>
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                        <div class="avatar-placeholder" id="avatar_preview_container"
                            style="width: 100px; height: 100px; border-radius: 1rem;">
                            <i data-lucide="user" class="h-10 w-10 text-slate-700" id="avatar_icon"></i>
                            <img id="avatar_preview" class="h-full w-full object-cover rounded-xl" style="display: none;"
                                src="" alt="Preview">
                        </div>
                        <input type="file" name="profile_photo" id="profile_photo_input" accept="image/*"
                            style="display: none;">
                        <button type="button" class="btn-secondary w-full" id="upload_photo_btn"
                            style="font-size: 0.75rem; padding: 0.5rem;">Upload Photo</button>
                    </div>
                </div>

                <div class="form-card">
                    <div class="form-section-header">
                        <i data-lucide="lock" class="h-5 w-5 text-teal-400"></i>
                        <h3>Account Security</h3>
                    </div>
                    <div class="form-group-stack">
                        <div class="form-group" style="margin-bottom: 1.25rem;">
                            <label class="form-label">Work Email Address:</label>
                            <input type="email" name="email" class="form-input" placeholder="name@via-architects.com"
                                value="{{ old('email') }}">
                        </div>
                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label">Password:</label>
                                <input type="password" name="password" class="form-input" placeholder="••••••••">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Confirm Password:</label>
                                <input type="password" name="password_confirmation" class="form-input"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM ACTIONS --}}
            <div style="margin-top: 3rem; display: flex; justify-content: center;">
                <button type="submit" class="btn-primary"
                    style="padding: 1.25rem 6rem; font-size: 1.125rem; font-weight: 700; border-radius: 1rem; background: linear-gradient(135deg, var(--primary) 0%, #2563eb 100%); border: none; box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4); transition: all 0.3s ease;">
                    <i data-lucide="save" class="h-6 w-6"></i>
                    Finalize Employee Record
                </button>
            </div>
    </div>
    </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // --- Salary Automation ---
            const posSelect = document.getElementById('position_select');
            const salInput = document.getElementById('salary_input');
            if (posSelect && salInput) {
                const sync = () => {
                    const opt = posSelect.options[posSelect.selectedIndex];
                    salInput.value = opt?.dataset?.salary ?? '';
                };
                posSelect.addEventListener('change', sync);
                if (posSelect.value) sync();
            }

            // --- Photo Preview ---
            const photoInput = document.getElementById('profile_photo_input');
            const avatarPreview = document.getElementById('avatar_preview');
            const avatarIcon = document.getElementById('avatar_icon');
            document.getElementById('upload_photo_btn').addEventListener('click', () => photoInput.click());
            photoInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        avatarPreview.src = e.target.result;
                        avatarPreview.style.display = 'block';
                        avatarIcon.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                }
            });

            // --- Government ID Auto-Masking ---
            const formatInput = (input, pattern) => {
                const applyFormat = () => {
                    let val = input.value.replace(/\D/g, '');
                    
                    // Strip the prefix if present in the raw input to avoid duplication
                    if (val.startsWith('639')) val = val.substring(3);
                    else if (val.startsWith('63')) val = val.substring(2);
                    else if (val.startsWith('9')) val = val.substring(1);

                    let formatted = '';
                    let valIdx = 0;
                    const firstZero = pattern.indexOf('0');

                    // Always start with the fixed part of the pattern
                    if (firstZero !== -1) {
                        formatted = pattern.substring(0, firstZero);
                        
                        for (let i = firstZero; i < pattern.length && valIdx < val.length; i++) {
                            if (pattern[i] === '0') {
                                formatted += val[valIdx++];
                            } else {
                                formatted += pattern[i];
                            }
                        }
                    } else {
                        // Fallback for patterns without '0'
                        for (let i = 0; i < pattern.length && valIdx < val.length; i++) {
                            if (pattern[i] === '0') {
                                formatted += val[valIdx++];
                            } else {
                                formatted += pattern[i];
                            }
                        }
                    }
                    input.value = formatted;
                };

                input.addEventListener('input', applyFormat);
                
                // Prevent user from deleting the fixed prefix
                input.addEventListener('keydown', (e) => {
                    const firstZero = pattern.indexOf('0');
                    if (firstZero !== -1 && input.selectionStart < firstZero) {
                        if (e.key === 'Backspace' || e.key === 'Delete' || e.key.length === 1) {
                            // If they try to type or delete before the prefix, jump to end of prefix
                            input.setSelectionRange(firstZero, firstZero);
                        }
                    }
                });

                applyFormat();
            };

            const sssInput = document.getElementById('sss_input');
            const philInput = document.getElementById('philhealth_input');
            const pagibigInput = document.getElementById('pagibig_input');
            const phoneInputMask = document.getElementById('phone_input');

            if (sssInput) formatInput(sssInput, '00-0000000-0');
            if (philInput) formatInput(philInput, '00-000000000-0');
            if (pagibigInput) formatInput(pagibigInput, '0000-0000-0000');
            if (phoneInputMask) formatInput(phoneInputMask, '+63 900 000 0000');
        });
    </script>
@endsection