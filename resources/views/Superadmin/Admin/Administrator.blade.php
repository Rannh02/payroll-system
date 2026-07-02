@extends('Superadmin.layouts.master')

@section('title', 'Administrator - Control Deck')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/superadmin/superadmin-administrator.css') }}">
@endsection

@section('content')
    <div class="superadmin-administrator-page">

        <div class="superadmin-administrator-hero">
            <div>
                <h2 style="margin: 0; font-size: 1.6rem; font-weight: 700;">Administrator Control Center</h2>
                <p style="margin: 0.3rem 0 0; color: #cbd5e1;">Create and manage enterprise admin accounts with secure credentials.</p>
            </div>
            <div class="superadmin-administrator-badge">
                Super Admin Workspace
            </div>
        </div>

        @if(session('success'))
            <div class="superadmin-administrator-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="superadmin-administrator-alert-error">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="superadmin-administrator-grid">
            <div class="superadmin-administrator-card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700;">Create New Admin</h3>
                        <p style="margin: 0.25rem 0 0; color: #64748b; font-size: 0.95rem;">Set up a new administrator account with secure login access.</p>
                    </div>
                    <div style="padding: 0.45rem 0.8rem; border-radius: 999px; background: #eff6ff; color: #2563eb; font-weight: 700; font-size: 0.8rem;">ERP Access</div>
                </div>

                <form action="{{ route('superadmin.Administrator.store') }}" method="POST">
                    @csrf
                    <div class="superadmin-administrator-form-grid">
                        <div class="superadmin-administrator-name-grid">
                            <div>
                                <label class="superadmin-administrator-label">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" required class="superadmin-administrator-input">
                            </div>

                            <div>
                                <label class="superadmin-administrator-label">Middle Name</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="superadmin-administrator-input">
                            </div>

                            <div>
                                <label class="superadmin-administrator-label">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" required class="superadmin-administrator-input">
                            </div>
                        </div>

                        <div>
                            <label class="superadmin-administrator-label">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="superadmin-administrator-input">
                        </div>

                        <div>
                            <label class="superadmin-administrator-label">Password</label>
                            <input type="password" id="admin_password" name="password" required class="superadmin-administrator-input">
                            <div id="admin-pw-strength-bar-wrap" class="superadmin-pw-strength">
                                <div class="superadmin-pw-segment-row">
                                    <div id="admin-pw-seg-1" class="superadmin-pw-segment"></div>
                                    <div id="admin-pw-seg-2" class="superadmin-pw-segment"></div>
                                    <div id="admin-pw-seg-3" class="superadmin-pw-segment"></div>
                                </div>
                                <p id="admin-pw-strength-text" class="superadmin-pw-text"></p>
                                <p id="admin-pw-strength-hint" class="superadmin-pw-hint"></p>
                            </div>
                            <p style="margin: 0.4rem 0 0; color: #64748b; font-size: 0.85rem;">Use 8–16 characters with uppercase, lowercase, a number, and a special character.</p>
                        </div>

                        <div>
                            <label class="superadmin-administrator-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" required class="superadmin-administrator-input">
                        </div>

                        <button type="submit" class="superadmin-administrator-button">Create Admin Account</button>
                    </div>
                </form>
            </div>

            <div class="superadmin-administrator-card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700;">Admin Accounts</h3>
                        <p style="margin: 0.25rem 0 0; color: #64748b; font-size: 0.95rem;">Authorized Admin for the VIA Architects Payroll</p>
                    </div>
                    <div class="superadmin-admin-count">{{ $admins->count() }} Total</div>
                </div>

                <table class="superadmin-admin-table">
                    <thead>
                        <tr style="border-bottom: 1px solid #e2e8f0; text-align: left; color: #64748b; font-size: 0.9rem;">
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                            <tr>
                                <td class="superadmin-admin-table-name">{{ $admin->name }}</td>
                                <td class="superadmin-admin-table-email">{{ $admin->email }}</td>
                                <td><span class="superadmin-admin-role">{{ ucfirst($admin->role) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="padding: 1rem 0.5rem; text-align: center; color: #64748b;">No admin accounts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <script src="{{ asset('js/superadmin/superadmin-administrator.js') }}"></script>
@endsection