@extends('layouts.master')

@section('title', 'Archived Employees - VIA Architects Associates')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/manage-employee.css') }}">
@endsection

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="content-header">
            <div>
                <h2 class="header-title">Archived Employees</h2>
                <p class="header-subtitle">
                    <span class="subtitle-dot"></span>
                    Manage and restore archived employee records.
                </p>
            </div>
            <div>
                <a href="{{ route('employees.index') }}" class="btn-secondary" title="Back to active employees">
                    <i data-lucide="users" class="h-4 w-4"></i>
                    Back to Active Employees
                </a>
            </div>
        </div>

        @if(session('success'))
            <div style="background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;padding:12px 16px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                <i data-lucide="check-circle" class="h-4 w-4"></i> {{ session('success') }}
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
                        <th>Hire Date</th>
                        <th>Archived Date</th>
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
                                </div>
                            </div>
                        </td>
                        <td class="employee-position">{{ $employee->position->position_name ?? 'N/A' }}</td>
                        <td>
                            <span class="department-badge badge-{{ strtolower(str_replace(' ', '-', $employee->department->department_name ?? 'none')) }}">
                                {{ $employee->department->department_name ?? 'N/A' }}
                            </span>
                        </td>
                        <td>{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('Y-m-d') : 'N/A' }}</td>
                        <td style="color: #ef4444; font-weight: 600;">{{ $employee->deleted_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="employee-action-group">
                                <button type="button" 
                                        class="employee-action-link action-view"
                                        onclick="openViewModal({{ json_encode([
                                            'db_id' => $employee->employee_id,
                                            'id' => $employee->employee_number ?? 'E' . str_pad($employee->employee_id, 3, '0', STR_PAD_LEFT),
                                            'name' => $employee->first_name . ' ' . $employee->last_name,
                                            'photo_url' => $employee->photo_url,
                                            'email' => $employee->user->email ?? 'N/A',
                                            'phone' => $employee->contact_info ?? 'N/A',
                                            'position' => $employee->position->position_name ?? 'N/A',
                                            'department' => $employee->department->department_name ?? 'N/A',
                                            'salary' => '₱' . number_format($employee->salary_rate ?? $employee->position->basic_salary ?? 0, 2),
                                            'status' => $employee->employment_status ?? 'Regular',
                                            'hire-date' => $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') : 'N/A',
                                            'sex' => $employee->sex ?? 'N/A',
                                            'birth' => $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('M d, Y') : 'N/A',
                                            'address' => ($employee->current_street_address ?? '') . ' ' . ($employee->current_barangay ?? '') . ' ' . ($employee->current_city_municipality ?? ''),
                                            'sss' => $employee->sss_num ?? 'N/A',
                                            'philhealth' => $employee->philhealth_num ?? 'N/A',
                                            'pagibig' => $employee->pagibig_num ?? 'N/A'
                                        ]) }})">
                                    View
                                </button>
                                <button type="button" 
                                        class="employee-action-link action-edit" 
                                        style="color: #16a34a;"
                                        onclick="openRestoreModal('{{ $employee->employee_id }}', '{{ $employee->first_name }} {{ $employee->last_name }}')">
                                    Restore
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                                <i data-lucide="archive" class="h-12 w-12 opacity-20"></i>
                                <p>No archived employees found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Restore Confirmation Modal -->
    <div id="restore-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-inner">
                <div class="modal-top">
                    <div class="modal-icon-container">
                        <i data-lucide="rotate-ccw" class="h-6 w-6"></i>
                    </div>
                    <div class="modal-info">
                        <h3 class="modal-title">Restore Employee</h3>
                        <p class="modal-description">
                            Are you sure you want to restore <strong id="employee-name-display" style="color: inherit; font-weight: 700;"></strong>? 
                            They will be moved back to the active employee list.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal btn-modal-secondary" onclick="closeRestoreModal()">Cancel</button>
                <form id="restore-form" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-modal btn-modal-success">Restore Employee</button>
                </form>
            </div>
        </div>
    </div>

    <!-- View Modal -->
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
                    <div class="section-divider">
                        <span>Personal Information</span>
                        <div class="section-line"></div>
                    </div>
                    
                    <div class="view-card">
                        <div class="view-card-icon"><i data-lucide="user"></i></div>
                        <div class="view-card-content">
                            <p class="view-card-label">Full Name</p>
                            <p class="view-card-value" id="view-name">---</p>
                        </div>
                    </div>
                    <div class="view-card">
                        <div class="view-card-icon"><i data-lucide="mail"></i></div>
                        <div class="view-card-content">
                            <p class="view-card-label">Email Address</p>
                            <p class="view-card-value" id="view-email">---</p>
                        </div>
                    </div>
                    <div class="view-card">
                        <div class="view-card-icon"><i data-lucide="phone"></i></div>
                        <div class="view-card-content">
                            <p class="view-card-label">Phone Number</p>
                            <p class="view-card-value" id="view-phone">---</p>
                        </div>
                    </div>
                    <div class="view-card">
                        <div class="view-card-icon"><i data-lucide="users"></i></div>
                        <div class="view-card-content">
                            <p class="view-card-label">Sex</p>
                            <p class="view-card-value" id="view-sex">---</p>
                        </div>
                    </div>
                    <div class="view-card">
                        <div class="view-card-icon"><i data-lucide="calendar"></i></div>
                        <div class="view-card-content">
                            <p class="view-card-label">Date of Birth</p>
                            <p class="view-card-value" id="view-birth">---</p>
                        </div>
                    </div>
                    <div class="view-card">
                        <div class="view-card-icon"><i data-lucide="map-pin"></i></div>
                        <div class="view-card-content">
                            <p class="view-card-label">Address</p>
                            <p class="view-card-value" id="view-address">---</p>
                        </div>
                    </div>

                    <div class="section-divider">
                        <span>Employment Details</span>
                        <div class="section-line"></div>
                    </div>
                    
                    <div class="view-card">
                        <div class="view-card-icon"><i data-lucide="award"></i></div>
                        <div class="view-card-content">
                            <p class="view-card-label">Department</p>
                            <p class="view-card-value" id="view-department">---</p>
                        </div>
                    </div>
                    <div class="view-card">
                        <div class="view-card-icon"><i data-lucide="dollar-sign"></i></div>
                        <div class="view-card-content">
                            <p class="view-card-label">Monthly Salary</p>
                            <p class="view-card-value" id="view-salary">---</p>
                        </div>
                    </div>
                    <div class="view-card">
                        <div class="view-card-icon"><i data-lucide="shield-check"></i></div>
                        <div class="view-card-content">
                            <p class="view-card-label">Employment Status</p>
                            <p class="view-card-value" id="view-status">---</p>
                        </div>
                    </div>
                    <div class="view-card">
                        <div class="view-card-icon"><i data-lucide="calendar-check"></i></div>
                        <div class="view-card-content">
                            <p class="view-card-label">Hire Date</p>
                            <p class="view-card-value" id="view-hire-date">---</p>
                        </div>
                    </div>

                    <div class="section-divider">
                        <span>Government Identifiers</span>
                        <div class="section-line"></div>
                    </div>
                    
                    <div class="view-card">
                        <div class="view-card-icon"><i data-lucide="fingerprint"></i></div>
                        <div class="view-card-content">
                            <p class="view-card-label">SSS Number</p>
                            <p class="view-card-value" id="view-sss">---</p>
                        </div>
                    </div>
                    <div class="view-card">
                        <div class="view-card-icon"><i data-lucide="heart"></i></div>
                        <div class="view-card-content">
                            <p class="view-card-label">PhilHealth</p>
                            <p class="view-card-value" id="view-philhealth">---</p>
                        </div>
                    </div>
                    <div class="view-card">
                        <div class="view-card-icon"><i data-lucide="home"></i></div>
                        <div class="view-card-content">
                            <p class="view-card-label">Pag-IBIG</p>
                            <p class="view-card-value" id="view-pagibig">---</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 1.25rem 1.5rem;">
                <button type="button" class="btn-modal btn-modal-secondary px-8" onclick="closeViewModal()" style="width: 100%;">Close Profile</button>
            </div>
        </div>
    </div>

    <script>
        function openRestoreModal(id, name) {
            const modal = document.getElementById('restore-modal');
            const form = document.getElementById('restore-form');
            const nameDisplay = document.getElementById('employee-name-display');
            
            form.action = `/employees/${id}/restore`;
            nameDisplay.textContent = name;
            
            modal.classList.add('show');
            if (window.lucide) window.lucide.createIcons();
        }

        function closeRestoreModal() {
            document.getElementById('restore-modal').classList.remove('show');
        }

        window.openViewModal = function(data) {
            const modal = document.getElementById('view-modal');
            
            // Populate Avatar and Header
            const avatarImg = document.getElementById('view-header-avatar-img');
            const avatarInitial = document.getElementById('view-header-avatar-initial');
            
            if (data.photo_url && !data.photo_url.includes('ui-avatars.com')) {
                avatarImg.src = data.photo_url;
                avatarImg.classList.remove('hidden');
                avatarInitial.classList.add('hidden');
            } else {
                avatarImg.classList.add('hidden');
                avatarInitial.classList.remove('hidden');
                avatarInitial.textContent = data.name.charAt(0).toUpperCase();
            }
            
            document.getElementById('view-header-name').textContent = data.name;
            document.getElementById('view-header-position').textContent = data.position;
            document.getElementById('view-header-id').textContent = `ID: ${data.id}`;
            
            // Populate Cards
            for (const key in data) {
                const el = document.getElementById(`view-${key}`);
                if (el) el.textContent = data[key];
            }
            modal.classList.add('show');
            if (window.lucide) window.lucide.createIcons();
        }

        function closeViewModal() {
            document.getElementById('view-modal').classList.remove('show');
        }

        window.onclick = function(event) {
            const restoreModal = document.getElementById('restore-modal');
            const viewModal = document.getElementById('view-modal');
            if (event.target == restoreModal) closeRestoreModal();
            if (event.target == viewModal) closeViewModal();
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRestoreModal();
                closeViewModal();
            }
        });
    </script>
@endsection
