@extends('layouts.master')

@section('title', 'Positions')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/position.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('js/admin/position.js') }}"></script>
@endsection

@section('content')
<div class="govt-container">
    <div class="content-header">
        <div>
            <h2 class="header-title">Positions</h2>
            <p class="header-subtitle">Manage job roles and compensation structures</p>
        </div>
        <div>
            <button class="btn-primary" onclick="openModal()">
                <i data-lucide="plus" class="h-4 w-4"></i>
                Add Position
            </button>
        </div>
    </div>

    <x-alert />

    @if(session('success'))
        <div style="background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;padding:12px 16px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <i data-lucide="check-circle" class="h-4 w-4"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;padding:12px 16px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <i data-lucide="alert-circle" class="h-4 w-4"></i> {{ session('error') }}
        </div>
    @endif

    <div class="position-table-container">
        <table class="position-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Position Name</th>
                    <th>Department</th>
                    <th>Monthly Salary</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($positions as $pos)
                    <tr>
                        <td><span class="badge-sss">{{ $pos->position_code }}</span></td>
                        <td style="font-weight: 600; color: var(--text-main);">{{ $pos->position_name }}</td>
                        <td>
                            @if($pos->department)
                                <span class="department-badge badge-{{ strtolower(str_replace(' ', '-', $pos->department->department_name)) }}">
                                    {{ $pos->department->department_name }}
                                </span>
                            @else
                                <span style="color: var(--text-muted);">N/A</span>
                            @endif
                        </td>
                        <td style="font-weight: 700; color: #059669;">₱{{ number_format($pos->basic_salary, 2) }}</td>
                        <td>
                            <span class="status-badge badge-{{ strtolower($pos->status) }}">
                                {{ $pos->status }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <form action="{{ route('position.destroy', $pos->position_id) }}" method="POST"
                                    onsubmit="return confirm('Delete this position?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="position-action-link delete-link">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">No positions found. Click <strong>Add Position</strong> to get started.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add Modal --}}
<div id="positionModal" class="modal-backdrop">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add Position</h3>
            <button class="btn-close" onclick="closeModal()"><i data-lucide="x"></i></button>
        </div>
        <form class="modal-form" action="{{ route('position.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Position Code <span class="required">*</span></label>
                    <input type="text" name="position_code" class="form-input" placeholder="e.g. DEV" value="{{ old('position_code') }}" required>
                </div>
                <div class="form-group">
                    <label>Position Name <span class="required">*</span></label>
                    <input type="text" name="position_name" class="form-input" placeholder="e.g. Developer" value="{{ old('position_name') }}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->department_id }}" {{ old('department_id') == $dept->department_id ? 'selected' : '' }}>{{ $dept->department_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Monthly Salary (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="basic_salary" class="form-input" placeholder="e.g. 25000.00" value="{{ old('basic_salary') }}" required>
                </div>
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
                <button type="submit" class="btn-primary">Save Position</button>
            </div>
        </form>
    </div>
</div>
@endsection

