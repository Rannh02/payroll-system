@extends('layouts.master')

@section('title', 'User Management - VIA Architects Associates')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/usermanagement/userall.css') }}?v={{ filemtime(public_path('css/usermanagement/userall.css')) }}">
@endsection

@section('content')
    <div class="user-mgmt-container">
        <!-- Content Header -->
        <div class="content-header" style="margin-bottom: 2rem;">
            <div>
                <h2 class="header-title">User Management</h2>
                <p class="header-subtitle">
                    <span class="subtitle-dot"></span>
                    Manage user access credentials, roles, and suspension states.
                </p>
            </div>
            <div>
                <a href="{{ route('it_admin.users.create_edit') }}" class="btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                    <i data-lucide="user-plus" class="h-4 w-4"></i>
                    Add New User
                </a>
            </div>
        </div>

        @if(session('success'))
            <div style="background:#d1fae5; color:#065f46; border:1px solid #6ee7b7; padding:12px 16px; border-radius:8px; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                <i data-lucide="check-circle" class="h-4 w-4"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background:#fee2e2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:16px;">
                <p style="font-weight:700; margin-bottom:0.5rem; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="alert-circle" class="h-4 w-4"></i> Errors occurred:
                </p>
                <ul style="font-size:0.8125rem; list-style:inside; padding-left:4px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Filter Card -->
        <form method="GET" action="{{ route('it_admin.users') }}" class="filter-card">
            <input type="text" name="search" placeholder="Search by name or email..." class="filter-input" value="{{ request('search') }}">
            
            <select name="role" class="filter-select" onchange="this.form.submit()">
                <option value="">All Roles</option>
                <option value="superadmin" {{ request('role') === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="it_admin" {{ request('role') === 'it_admin' ? 'selected' : '' }}>IT Admin</option>
                <option value="hr_admin" {{ request('role') === 'hr_admin' ? 'selected' : '' }}>HR Admin</option>
                <option value="finance_admin" {{ request('role') === 'finance_admin' ? 'selected' : '' }}>Finance Admin</option>
                <option value="employee" {{ request('role') === 'employee' ? 'selected' : '' }}>Employee</option>
            </select>

            <button type="submit" class="btn-secondary" style="padding: 0.625rem 1.25rem;">
                <i data-lucide="search" class="h-4 w-4" style="margin-right:0.25rem;"></i> Search
            </button>
            
            @if(request()->filled('search') || request()->filled('role'))
                <a href="{{ route('it_admin.users') }}" class="btn-secondary" style="padding: 0.625rem 1.25rem; text-decoration:none; display:inline-flex; align-items:center;">
                    Clear Filters
                </a>
            @endif
        </form>

        <!-- User Table Wrapper -->
        <div class="user-table-wrapper">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="user-avatar-cell">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="user-name">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="user-email">{{ $user->email }}</div>
                            </td>
                            <td>
                                <span class="role-badge role-badge-{{ $user->role }}">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td>
                                @if($user->is_suspended)
                                    <span class="status-badge status-badge-suspended">Suspended</span>
                                @else
                                    <span class="status-badge status-badge-active">Active</span>
                                @endif
                            </td>
                            <td style="color: #64748b; font-size: 0.8125rem;">
                                {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td>
                                <div class="actions-group">
                                    <!-- Edit Link -->
                                    <a href="{{ route('it_admin.users.create_edit', $user->id) }}" class="btn-action btn-action-edit" title="Edit user details">
                                        <i data-lucide="edit" class="h-3.5 w-3.5"></i> Edit
                                    </a>

                                    @if(Auth::id() !== $user->id && $user->role !== 'superadmin')
                                        <!-- Toggle Suspend Form -->
                                        <form method="POST" action="{{ route('it_admin.users.toggle-suspend', $user->id) }}" style="margin:0;">
                                            @csrf
                                            @if($user->is_suspended)
                                                <button type="submit" class="btn-action btn-action-activate" title="Activate Account">
                                                    <i data-lucide="unlock" class="h-3.5 w-3.5"></i> Activate
                                                </button>
                                            @else
                                                <button type="submit" class="btn-action btn-action-suspend" title="Suspend Account" onclick="return confirm('Are you sure you want to suspend this user?')">
                                                    <i data-lucide="lock" class="h-3.5 w-3.5"></i> Suspend
                                                </button>
                                            @endif
                                        </form>

                                        <!-- Delete Trigger Button -->
                                        <button type="button" class="btn-action btn-action-delete" title="Delete User Account" onclick="openDeleteModal('{{ $user->id }}', '{{ $user->name }}')">
                                            <i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Delete
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 4rem 1.5rem; color: #64748b;">
                                <div style="display:flex; flex-direction:column; align-items:center; gap:1rem;">
                                    <i data-lucide="users" class="h-12 w-12 opacity-20"></i>
                                    <p>No user accounts found matching the criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Links -->
            @if($users->hasPages())
                <div class="pagination-container">
                    <div>
                        <p class="text-xs text-slate-500">
                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                        </p>
                    </div>
                    <div>
                        {{ $users->links('pagination::simple-tailwind') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal-overlay">
        <div class="modal-content" style="max-width:450px;">
            <div class="modal-inner">
                <div class="modal-top">
                    <div class="modal-icon-container" style="background:#fee2e2; color:#ef4444;">
                        <i data-lucide="alert-triangle" class="h-6 w-6"></i>
                    </div>
                    <div class="modal-info">
                        <h3 class="modal-title">Delete User Account</h3>
                        <p class="modal-description">
                            Are you sure you want to permanently delete <strong id="delete-user-name" style="font-weight:700;"></strong>? This action cannot be undone and will delete their login records.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal btn-modal-secondary" onclick="closeDeleteModal()">Cancel</button>
                <form id="delete-form" method="POST" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-modal btn-modal-danger" style="background:#ef4444; border-color:#ef4444; color:#ffffff;">Delete User</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function openDeleteModal(id, name) {
            const modal = document.getElementById('delete-modal');
            const nameDisplay = document.getElementById('delete-user-name');
            const form = document.getElementById('delete-form');

            nameDisplay.textContent = name;
            form.action = `/it_admin/users/${id}`;
            modal.classList.add('show');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('delete-modal');
            modal.classList.remove('show');
        }
    </script>
@endsection
