@extends('layouts.master')

@section('title', 'PhilHealth Contributions')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/govt-contributions.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/department.css') }}">
@endsection

@section('content')
<div class="govt-container">
    <div class="content-header">
        <div>
            <h2 class="header-title">PhilHealth Contributions</h2>
            <p class="header-subtitle">Manage PhilHealth contribution brackets</p>
        </div>
        <button class="btn-primary" onclick="openModal()">
            <i data-lucide="plus"></i> Add Bracket
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i data-lucide="check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <i data-lucide="alert-circle"></i>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="department-table-container">
        <table class="department-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Salary From (₱)</th>
                    <th>Salary To (₱)</th>
                    <th>Contribution Rate (%)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($philhealth as $index => $row)
                    <tr>
                        <td>{{ $philhealth->firstItem() + $index }}</td>
                        <td><span class="badge-ph">{{ number_format($row->salary_from, 2) }}</span></td>
                        <td><span class="badge-ph">{{ number_format($row->salary_to, 2) }}</span></td>
                        <td>{{ number_format($row->contribution_rate, 2) }}%</td>
                        <td>
                            <div class="action-buttons">
                                <button class="department-action-link"
                                    onclick="openEditModal(
                                        {{ $row->philhealth_id }},
                                        '{{ $row->salary_from }}',
                                        '{{ $row->salary_to }}',
                                        '{{ $row->contribution_rate }}'
                                    )">Edit</button>
                                <form action="{{ route('philhealth.destroy', $row->philhealth_id) }}" method="POST"
                                    onsubmit="return confirm('Delete this PhilHealth bracket?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="department-action-link delete-link">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">No PhilHealth brackets found. Click <strong>Add Bracket</strong> to get started.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $philhealth->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- Add Modal --}}
<div id="philhealthModal" class="modal-backdrop">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add PhilHealth Bracket</h3>
            <button class="btn-close" onclick="closeModal()"><i data-lucide="x"></i></button>
        </div>
        <form class="modal-form" action="{{ route('philhealth.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Salary From (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="salary_from" class="form-input" placeholder="e.g. 0.00" value="{{ old('salary_from') }}" required>
                </div>
                <div class="form-group">
                    <label>Salary To (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="salary_to" class="form-input" placeholder="e.g. 10000.00" value="{{ old('salary_to') }}" required>
                </div>
            </div>
            <div class="form-group-full half-width">
                <label>Contribution Rate (%) <span class="required">*</span></label>
                <input type="number" step="0.01" name="contribution_rate" class="form-input" placeholder="e.g. 4.00" value="{{ old('contribution_rate') }}" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-primary">Save Bracket</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="philhealthEditModal" class="modal-backdrop">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edit PhilHealth Bracket</h3>
            <button class="btn-close" onclick="closeEditModal()"><i data-lucide="x"></i></button>
        </div>
        <form class="modal-form" id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label>Salary From (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="salary_from" id="edit_salary_from" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Salary To (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="salary_to" id="edit_salary_to" class="form-input" required>
                </div>
            </div>
            <div class="form-group-full half-width">
                <label>Contribution Rate (%) <span class="required">*</span></label>
                <input type="number" step="0.01" name="contribution_rate" id="edit_contribution_rate" class="form-input" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-primary">Update Bracket</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal() {
        document.getElementById('philhealthModal').classList.add('show');
        lucide.createIcons();
    }
    function closeModal() {
        document.getElementById('philhealthModal').classList.remove('show');
    }
    function openEditModal(id, salaryFrom, salaryTo, rate) {
        const form = document.getElementById('editForm');
        form.action = `/philhealth/${id}`;
        document.getElementById('edit_salary_from').value = salaryFrom;
        document.getElementById('edit_salary_to').value = salaryTo;
        document.getElementById('edit_contribution_rate').value = rate;
        document.getElementById('philhealthEditModal').classList.add('show');
        lucide.createIcons();
    }
    function closeEditModal() {
        document.getElementById('philhealthEditModal').classList.remove('show');
    }
    window.onclick = function(e) {
        if (e.target == document.getElementById('philhealthModal')) closeModal();
        if (e.target == document.getElementById('philhealthEditModal')) closeEditModal();
    };
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => openModal());
    @endif
</script>
@endsection
