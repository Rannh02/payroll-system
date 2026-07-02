@extends('layouts.master')

@section('title', 'Departments')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/department.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('js/admin/department.js') }}"></script>
@endsection

@section('content')
<div class="govt-container">
    <div class="content-header">
        <div>
            <h2 class="header-title">Departments</h2>
            <p class="header-subtitle">Manage organizational departments and structures</p>
        </div>
        <button class="btn-primary" onclick="openModal()">
            <i data-lucide="plus"></i> Add Department
        </button>
    </div>

    <x-alert />

    <div class="department-table-container">
        <table class="department-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Department Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $dept)
                    <tr>
                        <td><span class="badge-sss">{{ $dept->department_code }}</span></td>
                        <td style="font-weight: 600; color: var(--text-main);">{{ $dept->department_name }}</td>
                        <td style="color: var(--text-muted);">{{ Str::limit($dept->description, 50) }}</td>
                        <td>
                            <span class="status-badge badge-{{ strtolower($dept->status) }}">
                                {{ $dept->status }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <form action="{{ route('department.destroy', $dept->department_id) }}" method="POST"
                                    onsubmit="return confirm('Delete this department?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="department-action-link delete-link">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">No departments found. Click <strong>Add Department</strong> to get started.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add Modal --}}
<div id="departmentModal" class="modal-backdrop">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add Department</h3>
            <button class="btn-close" onclick="closeModal()"><i data-lucide="x"></i></button>
        </div>
        <form class="modal-form" action="{{ route('department.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Department Code <span class="required">*</span></label>
                <input type="text" name="department_code" class="form-input" placeholder="e.g. ENG" value="{{ old('department_code') }}" required>
            </div>
            <div class="form-group">
                <label>Department Name <span class="required">*</span></label>
                <input type="text" name="department_name" class="form-input" placeholder="e.g. Engineering" value="{{ old('department_name') }}" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-input" placeholder="Brief description...">{{ old('description') }}</textarea>
            </div>
            <div class="form-group">
                <label>Status <span class="required">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-primary">Save Department</button>
            </div>
        </form>
    </div>
</div>
@endsection
