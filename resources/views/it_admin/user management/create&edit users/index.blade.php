@extends('it_admin.layouts.master')

@section('title', isset($user) ? 'Edit User — VIA Payroll' : 'Create User — VIA Payroll')

@section('styles')
<style>
    /* ── Page Layout ─────────────────────────────── */
    .create-edit-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 1.75rem;
        max-width: 1100px;
    }

    @media (max-width: 900px) {
        .create-edit-grid { grid-template-columns: 1fr; }
    }

    /* ── Form Card ───────────────────────────────── */
    .form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 24px -4px rgba(0,0,0,0.07);
        overflow: hidden;
    }

    .form-card-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(135deg, #f8fafc 0%, #f0f4ff 100%);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .form-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .form-card-icon-create { background: linear-gradient(135deg, #3b82f6, #6366f1); color: #fff; }
    .form-card-icon-edit   { background: linear-gradient(135deg, #8b5cf6, #ec4899); color: #fff; }

    .form-card-header h3 {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 0.15rem;
    }

    .form-card-header p {
        font-size: 0.8125rem;
        color: #64748b;
        margin: 0;
    }

    .form-body {
        padding: 2rem;
    }

    /* ── Field Groups ─────────────────────────────── */
    .field-group {
        margin-bottom: 1.5rem;
    }

    .field-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .field-label .required-star {
        color: #ef4444;
        margin-left: 2px;
    }

    .field-input,
    .field-select {
        width: 100%;
        padding: 0.65rem 1rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.875rem;
        color: #1e293b;
        background: #f8fafc;
        transition: all 0.2s;
        box-sizing: border-box;
    }

    .field-input:focus,
    .field-select:focus {
        outline: none;
        border-color: #3b82f6;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
    }

    .field-input.is-invalid,
    .field-select.is-invalid {
        border-color: #f87171;
        background: #fff5f5;
    }

    .field-error {
        font-size: 0.75rem;
        color: #dc2626;
        margin-top: 0.35rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .field-hint {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 0.35rem;
    }

    /* ── Password Wrapper ─────────────────────────── */
    .password-wrapper {
        position: relative;
    }

    .password-wrapper .field-input {
        padding-right: 3rem;
    }

    .password-toggle {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #94a3b8;
        display: flex;
        align-items: center;
        padding: 4px;
        border-radius: 4px;
        transition: color 0.15s;
    }

    .password-toggle:hover { color: #3b82f6; }

    /* ── Divider ──────────────────────────────────── */
    .form-divider {
        border: none;
        border-top: 1px dashed #e2e8f0;
        margin: 1.5rem 0;
    }

    .form-section-label {
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }

    /* ── Role Grid ────────────────────────────────── */
    .role-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }

    @media (max-width: 640px) {
        .role-grid { grid-template-columns: repeat(2, 1fr); }
    }

    .role-card-input { display: none; }

    .role-card-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 0.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        font-size: 0.75rem;
        font-weight: 600;
        color: #475569;
    }

    .role-card-label:hover {
        border-color: #93c5fd;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .role-card-input:checked + .role-card-label {
        border-color: #3b82f6;
        background: #eff6ff;
        color: #1d4ed8;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
    }

    .role-card-dot {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ── Form Actions ─────────────────────────────── */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding-top: 1.5rem;
        border-top: 1px solid #f1f5f9;
        margin-top: 1.5rem;
    }

    .btn-cancel {
        padding: 0.65rem 1.5rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #64748b;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s;
    }

    .btn-cancel:hover {
        background: #f1f5f9;
        color: #334155;
    }

    .btn-submit {
        padding: 0.65rem 1.75rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 700;
        color: #ffffff;
        background: linear-gradient(135deg, #3b82f6, #6366f1);
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        box-shadow: 0 4px 14px rgba(59,130,246,0.35);
        transition: all 0.2s;
    }

    .btn-submit:hover {
        box-shadow: 0 6px 20px rgba(59,130,246,0.45);
        transform: translateY(-1px);
    }

    /* ── Sidebar Panel ────────────────────────────── */
    .side-panel {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .side-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 12px -4px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .side-card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .side-card-body { padding: 0.75rem 1.25rem 1.25rem; }

    /* Recent users list */
    .recent-user-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .recent-user-item:last-child { border-bottom: none; }

    .recent-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }

    .recent-user-name {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #1e293b;
    }

    .recent-user-role {
        font-size: 0.6875rem;
        color: #64748b;
        margin-top: 1px;
    }

    /* Info list */
    .info-row {
        display: flex;
        align-items: flex-start;
        gap: 0.625rem;
        padding: 0.625rem 0;
        font-size: 0.8125rem;
        color: #475569;
        border-bottom: 1px solid #f8fafc;
    }

    .info-row:last-child { border-bottom: none; }

    .info-row i { color: #3b82f6; margin-top: 1px; flex-shrink: 0; }

    /* Avatar colors by role */
    .avatar-admin      { background: linear-gradient(135deg, #3b82f6, #6366f1); }
    .avatar-it_admin   { background: linear-gradient(135deg, #8b5cf6, #a855f7); }
    .avatar-hr_admin   { background: linear-gradient(135deg, #06b6d4, #0ea5e9); }
    .avatar-finance_admin { background: linear-gradient(135deg, #f59e0b, #f97316); }
    .avatar-employee   { background: linear-gradient(135deg, #10b981, #059669); }
    .avatar-superadmin { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .avatar-default    { background: linear-gradient(135deg, #94a3b8, #64748b); }

    /* Alert */
    .flash-alert {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.875rem 1.25rem;
        border-radius: 10px;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }

    .flash-success { background: #ecfdf5; color: #065f46; border: 1px solid #6ee7b7; }
    .flash-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
</style>
@endsection

@section('content')
<div style="padding: 1.5rem;">

    {{-- Page Header --}}
    <div class="content-header" style="margin-bottom: 2rem;">
        <div>
            <h2 class="header-title">
                {{ isset($user) ? 'Edit User Account' : 'Create User Account' }}
            </h2>
            <p class="header-subtitle">
                <span class="subtitle-dot"></span>
                {{ isset($user) ? 'Update credentials, role, or status for ' . $user->name : 'Add a new login account to the system.' }}
            </p>
        </div>
        <div>
            <a href="{{ route('it_admin.users') }}" class="btn-secondary"
               style="display:inline-flex; align-items:center; gap:0.5rem; text-decoration:none; padding: 0.625rem 1.25rem;">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Back to All Users
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flash-alert flash-success">
            <i data-lucide="check-circle" class="h-4 w-4"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('error'))
        <div class="flash-alert flash-error">
            <i data-lucide="alert-circle" class="h-4 w-4"></i>
            {{ $errors->first('error') }}
        </div>
    @endif

    <div class="create-edit-grid">

        {{-- ─── MAIN FORM CARD ──────────────────────────────── --}}
        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-icon {{ isset($user) ? 'form-card-icon-edit' : 'form-card-icon-create' }}">
                    <i data-lucide="{{ isset($user) ? 'user-check' : 'user-plus' }}" class="h-5 w-5"></i>
                </div>
                <div>
                    <h3>{{ isset($user) ? 'Edit Account Details' : 'New Account Details' }}</h3>
                    <p>{{ isset($user) ? 'Modify any field and save changes.' : 'Fill in all required fields to create a new login.' }}</p>
                </div>
            </div>

            <div class="form-body">
                @if(isset($user))
                    <form id="user-form" method="POST" action="{{ route('it_admin.users.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                @else
                    <form id="user-form" method="POST" action="{{ route('it_admin.users.store') }}">
                        @csrf
                @endif

                {{-- ── Basic Info ───────────────────────────── --}}
                <p class="form-section-label"><i data-lucide="info" class="h-3.5 w-3.5"></i> Basic Information</p>

                <div class="field-group">
                    <label class="field-label" for="name">
                        Full Name <span class="required-star">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="field-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                        value="{{ old('name', $user->name ?? '') }}"
                        placeholder="e.g. Juan dela Cruz"
                        required
                        autocomplete="name"
                    >
                    @error('name')
                        <p class="field-error"><i data-lucide="alert-circle" class="h-3 w-3"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label" for="email">
                        Email Address <span class="required-star">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="field-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        value="{{ old('email', $user->email ?? '') }}"
                        placeholder="e.g. juan@via.com.ph"
                        required
                        autocomplete="email"
                    >
                    @error('email')
                        <p class="field-error"><i data-lucide="alert-circle" class="h-3 w-3"></i> {{ $message }}</p>
                    @enderror
                </div>

                <hr class="form-divider">

                {{-- ── Role Selection ───────────────────────── --}}
                <p class="form-section-label"><i data-lucide="shield" class="h-3.5 w-3.5"></i> Assign Role</p>

                <div class="field-group">
                    <div class="role-grid">

                        @php
                            $roles = [
                                'admin'         => ['label' => 'Admin',         'icon' => 'shield',        'bg' => 'background:linear-gradient(135deg,#3b82f6,#6366f1)'],
                                'it_admin'      => ['label' => 'IT Admin',      'icon' => 'monitor',       'bg' => 'background:linear-gradient(135deg,#8b5cf6,#a855f7)'],
                                'hr_admin'      => ['label' => 'HR Admin',      'icon' => 'users',         'bg' => 'background:linear-gradient(135deg,#06b6d4,#0ea5e9)'],
                                'finance_admin' => ['label' => 'Finance Admin', 'icon' => 'landmark',      'bg' => 'background:linear-gradient(135deg,#f59e0b,#f97316)'],
                                'employee'      => ['label' => 'Employee',      'icon' => 'user',          'bg' => 'background:linear-gradient(135deg,#10b981,#059669)'],
                            ];
                            $selectedRole = old('role', $user->role ?? '');
                        @endphp

                        @foreach($roles as $value => $meta)
                            <div>
                                <input
                                    type="radio"
                                    class="role-card-input"
                                    name="role"
                                    id="role_{{ $value }}"
                                    value="{{ $value }}"
                                    {{ $selectedRole === $value ? 'checked' : '' }}
                                    required
                                >
                                <label class="role-card-label" for="role_{{ $value }}">
                                    <span class="role-card-dot" style="{{ $meta['bg'] }}; color:#fff;">
                                        <i data-lucide="{{ $meta['icon'] }}" class="h-4 w-4"></i>
                                    </span>
                                    {{ $meta['label'] }}
                                </label>
                            </div>
                        @endforeach

                    </div>
                    @error('role')
                        <p class="field-error" style="margin-top:0.5rem;"><i data-lucide="alert-circle" class="h-3 w-3"></i> {{ $message }}</p>
                    @enderror
                </div>

                <hr class="form-divider">

                {{-- ── Password ──────────────────────────────── --}}
                <p class="form-section-label"><i data-lucide="lock" class="h-3.5 w-3.5"></i> Password</p>

                <div class="field-group">
                    <label class="field-label" for="password">
                        @if(isset($user))
                            New Password <span style="font-weight:400; color:#94a3b8;">(leave blank to keep current)</span>
                        @else
                            Password <span class="required-star">*</span>
                        @endif
                    </label>
                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="field-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="{{ isset($user) ? '••••••••' : 'Minimum 8 characters' }}"
                            {{ isset($user) ? '' : 'required' }}
                            autocomplete="new-password"
                        >
                        <button type="button" class="password-toggle" onclick="togglePwd('password', this)" aria-label="Show password">
                            <i data-lucide="eye" class="h-4 w-4"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="field-error"><i data-lucide="alert-circle" class="h-3 w-3"></i> {{ $message }}</p>
                    @enderror
                    <p class="field-hint">Password must be at least 8 characters long.</p>
                </div>

                <div class="field-group">
                    <label class="field-label" for="password_confirmation">
                        Confirm Password {{ !isset($user) ? '*' : '' }}
                    </label>
                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="field-input"
                            placeholder="Repeat the password"
                            autocomplete="new-password"
                        >
                        <button type="button" class="password-toggle" onclick="togglePwd('password_confirmation', this)" aria-label="Show confirm password">
                            <i data-lucide="eye" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>

                {{-- ── Actions ───────────────────────────────── --}}
                <div class="form-actions">
                    <a href="{{ route('it_admin.users') }}" class="btn-cancel">
                        <i data-lucide="x" class="h-4 w-4"></i> Cancel
                    </a>
                    <button type="submit" class="btn-submit" id="submit-btn">
                        <i data-lucide="{{ isset($user) ? 'save' : 'user-plus' }}" class="h-4 w-4"></i>
                        {{ isset($user) ? 'Save Changes' : 'Create Account' }}
                    </button>
                </div>

                </form>
            </div>{{-- end .form-body --}}
        </div>{{-- end .form-card --}}

        {{-- ─── SIDEBAR PANELS ──────────────────────────────── --}}
        <div class="side-panel">

            {{-- Editing banner (when in edit mode) --}}
            @if(isset($user))
            <div class="side-card" style="border-color: #c7d2fe; background: linear-gradient(135deg, #eef2ff, #f0f9ff);">
                <div class="side-card-header" style="background: transparent; border-bottom-color: #c7d2fe; color: #4338ca;">
                    <i data-lucide="pencil" class="h-4 w-4"></i> Editing Account
                </div>
                <div class="side-card-body">
                    <div style="display:flex; align-items:center; gap:0.875rem;">
                        <div class="recent-avatar avatar-{{ $user->role ?? 'default' }}" style="width:48px; height:48px; font-size:1rem; border-radius:12px;">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div>
                            <div style="font-weight:700; color:#1e293b; font-size:0.9375rem;">{{ $user->name }}</div>
                            <div style="font-size:0.75rem; color:#64748b;">{{ $user->email }}</div>
                            <div style="margin-top:4px;">
                                <span style="display:inline-block; padding:2px 10px; border-radius:9999px; font-size:0.6875rem; font-weight:700; background:#e0f2fe; color:#0369a1;">
                                    {{ str_replace('_', ' ', strtoupper($user->role ?? '')) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 1rem; font-size: 0.75rem; color: #64748b; background: #fff; border-radius: 8px; padding: 0.625rem;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                            <span>Account Created</span>
                            <strong>{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span>Status</span>
                            <strong style="color: {{ $user->is_suspended ? '#dc2626' : '#059669' }}">
                                {{ $user->is_suspended ? 'Suspended' : 'Active' }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Recent Accounts --}}
            <div class="side-card">
                <div class="side-card-header">
                    <i data-lucide="clock" class="h-4 w-4" style="color:#3b82f6;"></i>
                    Recently Added
                </div>
                <div class="side-card-body">
                    @forelse($recentUsers as $ru)
                    <div class="recent-user-item">
                        <div class="recent-avatar avatar-{{ $ru->role ?? 'default' }}">
                            {{ strtoupper(substr($ru->name, 0, 2)) }}
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div class="recent-user-name text-truncate">{{ $ru->name }}</div>
                            <div class="recent-user-role">{{ str_replace('_', ' ', $ru->role ?? '') }}</div>
                        </div>
                        <a href="{{ route('it_admin.users.create_edit', $ru->id) }}"
                           style="color:#3b82f6; font-size:0.75rem; text-decoration:none; font-weight:600; white-space:nowrap;">
                            Edit
                        </a>
                    </div>
                    @empty
                    <p style="font-size:0.8125rem; color:#94a3b8; text-align:center; padding: 1rem 0;">No users found.</p>
                    @endforelse
                </div>
            </div>

            {{-- Quick Tips --}}
            <div class="side-card">
                <div class="side-card-header">
                    <i data-lucide="lightbulb" class="h-4 w-4" style="color:#f59e0b;"></i>
                    Quick Tips
                </div>
                <div class="side-card-body">
                    <div class="info-row">
                        <i data-lucide="shield-check" class="h-3.5 w-3.5"></i>
                        <span>Admin-type roles log in through the <strong>Admin Login</strong> portal.</span>
                    </div>
                    <div class="info-row">
                        <i data-lucide="user" class="h-3.5 w-3.5"></i>
                        <span>Employees log in through the <strong>Employee Login</strong> portal.</span>
                    </div>
                    <div class="info-row">
                        <i data-lucide="refresh-cw" class="h-3.5 w-3.5"></i>
                        <span>Changing a role automatically migrates the auth record across tables.</span>
                    </div>
                    <div class="info-row">
                        <i data-lucide="lock" class="h-3.5 w-3.5"></i>
                        <span>Passwords must be at least 8 characters. Leaving blank retains the old one.</span>
                    </div>
                </div>
            </div>

        </div>{{-- end .side-panel --}}
    </div>{{-- end .create-edit-grid --}}
</div>
@endsection

@section('scripts')
<script>
    // Toggle password visibility
    function togglePwd(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        if (window.lucide) window.lucide.createIcons();
    }

    // Confirm role-change warning when editing
    @if(isset($user))
    document.getElementById('user-form').addEventListener('submit', function(e) {
        const selectedRole = document.querySelector('input[name="role"]:checked');
        const originalRole = '{{ $user->role }}';
        if (selectedRole && selectedRole.value !== originalRole) {
            const isAdminRoles = ['admin', 'it_admin', 'hr_admin', 'finance_admin'];
            const fromAdmin = isAdminRoles.includes(originalRole);
            const toAdmin   = isAdminRoles.includes(selectedRole.value);
            if (fromAdmin !== toAdmin) {
                const msg = fromAdmin
                    ? 'Changing to Employee will move this user from the Admin portal to the Employee portal. Continue?'
                    : 'Changing to an Admin role will move this user from the Employee portal to the Admin portal. Continue?';
                if (!confirm(msg)) { e.preventDefault(); }
            }
        }
    });
    @endif
</script>
@endsection
