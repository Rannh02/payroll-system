@extends('layouts.master')

@section('title', 'Roles & Permissions — VIA Payroll')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/usermanagement/role&permission.css') }}">
@endsection

@section('content')
<div class="rp-container">

    {{-- Page Header --}}
    <div class="content-header" style="margin-bottom: 2rem;">
        <div>
            <h2 class="header-title">Roles & Permissions</h2>
            <p class="header-subtitle">
                <span class="subtitle-dot"></span>
                Overview of system roles and their module-level access rights.
            </p>
        </div>
        <div>
            <a href="{{ route('it_admin.users.create_edit') }}"
               class="btn-primary"
               style="display:inline-flex; align-items:center; gap:0.5rem; text-decoration:none;">
                <i data-lucide="user-plus" class="h-4 w-4"></i> Add User
            </a>
        </div>
    </div>

    {{-- ── ROLE CARDS ───────────────────────────── --}}
    @php
        use App\Models\User;

        $roleMeta = [
            'superadmin' => [
                'label'    => 'Super Admin',
                'subtitle' => 'Unrestricted system access',
                'icon'     => 'shield-alert',
                'bg'       => 'background:linear-gradient(135deg,#ef4444,#dc2626)',
                'perms'    => [
                    'Full system access',
                    'Manage all admins',
                    'View audit & security logs',
                    'Configure system settings',
                    'Suspend any account',
                ],
            ],
            'admin' => [
                'label'    => 'Admin',
                'subtitle' => 'General admin capabilities',
                'icon'     => 'shield',
                'bg'       => 'background:linear-gradient(135deg,#3b82f6,#6366f1)',
                'perms'    => [
                    'Employee management',
                    'Payroll processing',
                    'Leave approvals',
                    'Reports & analytics',
                    'Attendance tracking',
                ],
            ],
            'it_admin' => [
                'label'    => 'IT Admin',
                'subtitle' => 'System & user management',
                'icon'     => 'monitor',
                'bg'       => 'background:linear-gradient(135deg,#8b5cf6,#a855f7)',
                'perms'    => [
                    'Manage user accounts',
                    'Assign roles',
                    'View security & login logs',
                    'Suspend/activate accounts',
                    'Configure access control',
                ],
            ],
            'hr_admin' => [
                'label'    => 'HR Admin',
                'subtitle' => 'People & workforce operations',
                'icon'     => 'users',
                'bg'       => 'background:linear-gradient(135deg,#06b6d4,#0ea5e9)',
                'perms'    => [
                    'Employee records',
                    'Leave management',
                    'Departments & positions',
                    'Attendance management',
                    'Employee reports',
                ],
            ],
            'finance_admin' => [
                'label'    => 'Finance Admin',
                'subtitle' => 'Payroll & financial operations',
                'icon'     => 'landmark',
                'bg'       => 'background:linear-gradient(135deg,#f59e0b,#f97316)',
                'perms'    => [
                    'Payroll processing',
                    'SSS / PhilHealth / Pag-IBIG',
                    'Tax management',
                    'Financial reports',
                    'Payslip generation',
                ],
            ],
            'employee' => [
                'label'    => 'Employee',
                'subtitle' => 'Self-service portal access',
                'icon'     => 'user',
                'bg'       => 'background:linear-gradient(135deg,#10b981,#059669)',
                'perms'    => [
                    'View own payslips',
                    'Clock in & out',
                    'Submit leave requests',
                    'View attendance history',
                    'Update profile',
                ],
            ],
        ];

        // Count per role from the users table
        $roleCounts = User::select('role', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                          ->groupBy('role')
                          ->pluck('total', 'role');
    @endphp

    <div class="roles-overview-grid">
        @foreach($roleMeta as $roleKey => $role)
        <div class="role-perm-card">
            <div class="role-card-head">
                <div class="role-hero-icon" style="{{ $role['bg'] }}">
                    <i data-lucide="{{ $role['icon'] }}" class="h-5 w-5"></i>
                </div>
                <div>
                    <h4 class="role-card-title">{{ $role['label'] }}</h4>
                    <p class="role-card-subtitle">{{ $role['subtitle'] }}</p>
                </div>
            </div>
            <div class="role-card-body">
                <ul class="perm-list">
                    @foreach($role['perms'] as $perm)
                    <li class="perm-item">
                        <span class="perm-check perm-check-yes">
                            <i data-lucide="check" class="h-3 w-3"></i>
                        </span>
                        {{ $perm }}
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="role-card-footer">
                <div class="user-count-badge">
                    <i data-lucide="users" class="h-3.5 w-3.5"></i>
                    <div>
                        <span class="user-count-num">{{ $roleCounts[$roleKey] ?? 0 }}</span>
                        user{{ ($roleCounts[$roleKey] ?? 0) !== 1 ? 's' : '' }} assigned
                    </div>
                </div>
                @if($roleKey !== 'superadmin')
                <a href="{{ route('it_admin.users', ['role' => $roleKey]) }}"
                   style="font-size:0.75rem; font-weight:600; color:#3b82f6; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    View Users <i data-lucide="arrow-right" class="h-3 w-3"></i>
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── PERMISSION MATRIX ─────────────────────── --}}
    <div class="matrix-card">
        <div class="matrix-card-header">
            <div style="width:36px; height:36px; border-radius:8px; background:linear-gradient(135deg,#3b82f6,#6366f1); display:flex; align-items:center; justify-content:center; color:#fff; flex-shrink:0;">
                <i data-lucide="table" class="h-4 w-4"></i>
            </div>
            <div>
                <h3>Module Access Matrix</h3>
                <p>Full cross-reference of roles vs. system modules.</p>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="matrix-table">
                <thead>
                    <tr>
                        <th>Module / Feature</th>
                        <th class="col-superadmin">Super<br>Admin</th>
                        <th class="col-admin">Admin</th>
                        <th class="col-it">IT<br>Admin</th>
                        <th class="col-hr">HR<br>Admin</th>
                        <th class="col-finance">Finance<br>Admin</th>
                        <th class="col-employee">Employee</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- Group: Dashboard --}}
                    <tr class="module-group-row"><td colspan="7">Dashboard & Analytics</td></tr>
                    @php
                        $matrix = [
                            // [Feature, Super, Admin, IT, HR, Finance, Employee]
                            // Dashboard
                            ['Admin Dashboard',        true,  true,  true,  true,  true,  false],
                            ['Employee Dashboard',      false, false, false, false, false, true],
                            ['Analytics & KPIs',        true,  true,  false, false, false, false],

                            // User Management
                            ['__USER MANAGEMENT'],
                            ['View All Users',          true,  false, true,  false, false, false],
                            ['Create User Account',     true,  false, true,  false, false, false],
                            ['Edit User Account',       true,  false, true,  false, false, false],
                            ['Delete User Account',     true,  false, true,  false, false, false],
                            ['Suspend / Activate',      true,  false, true,  false, false, false],
                            ['Assign Roles',            true,  false, true,  false, false, false],

                            // HR
                            ['__HR & PEOPLE'],
                            ['Employee Records',        true,  true,  false, true,  false, false],
                            ['Add / Edit Employee',     true,  true,  false, true,  false, false],
                            ['Departments & Positions', true,  true,  false, true,  false, false],
                            ['Leave Approvals',         true,  true,  false, true,  false, false],

                            // Attendance
                            ['__ATTENDANCE'],
                            ['Manage Attendance',       true,  true,  false, true,  false, false],
                            ['View Own Attendance',     false, false, false, false, false, true],
                            ['Clock In / Out',          false, false, false, false, false, true],

                            // Payroll & Finance
                            ['__PAYROLL & FINANCE'],
                            ['Run Payroll',             true,  true,  false, false, true,  false],
                            ['View Payslips (All)',     true,  true,  false, false, true,  false],
                            ['View Own Payslip',        false, false, false, false, false, true],
                            ['SSS / PhilHealth / Pag-IBIG', true, true, false, false, true, false],
                            ['Tax Management',          true,  true,  false, false, true,  false],

                            // Security
                            ['__SECURITY & LOGS'],
                            ['Security & Login Logs',   true,  false, true,  false, false, false],
                            ['Audit Logs',              true,  false, false, false, false, false],
                            ['Unlock Accounts',         true,  false, true,  false, false, false],

                            // Reports
                            ['__REPORTS'],
                            ['Generate Reports',        true,  true,  false, true,  true,  false],
                            ['Export Reports',          true,  true,  false, true,  true,  false],
                        ];
                    @endphp

                    @foreach($matrix as $row)
                        @if(Str::startsWith($row[0], '__'))
                            <tr class="module-group-row">
                                <td colspan="7">{{ ltrim($row[0], '_') }}</td>
                            </tr>
                        @else
                        <tr>
                            <td>{{ $row[0] }}</td>
                            @foreach(array_slice($row, 1) as $allowed)
                            <td>
                                @if($allowed)
                                    <span class="matrix-check-yes">
                                        <i data-lucide="check" class="h-3 w-3"></i>
                                    </span>
                                @else
                                    <span class="matrix-check-no">
                                        <i data-lucide="minus" class="h-3 w-3"></i>
                                    </span>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endif
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Legend ──────────────────────────────── --}}
    <div style="display:flex; align-items:center; gap:1.5rem; padding: 0.75rem 1.5rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; font-size:0.8125rem; color:#64748b;">
        <span style="font-weight:700; color:#334155;">Legend:</span>
        <span style="display:flex; align-items:center; gap:6px;">
            <span class="matrix-check-yes"><i data-lucide="check" class="h-3 w-3"></i></span>
            Access Granted
        </span>
        <span style="display:flex; align-items:center; gap:6px;">
            <span class="matrix-check-no"><i data-lucide="minus" class="h-3 w-3"></i></span>
            No Access
        </span>
        <span style="margin-left:auto; font-size:0.75rem;">
            <i data-lucide="info" class="h-3.5 w-3.5" style="vertical-align:middle;"></i>
            Access levels are defined by role assignment in the User Management module.
        </span>
    </div>

</div>
@endsection
