@extends('layouts.master')

@section('title', 'Pag-IBIG Contributions')

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
            <h2 class="header-title">Pag-IBIG Contributions</h2>
            <p class="header-subtitle">Manage Pag-IBIG (HDMF) contribution brackets</p>
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
                    <th>Employee Rate (%)</th>
                    <th>Employer Rate (%)</th>
                    <th>Max Contribution (₱)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pagibig as $index => $row)
                    <tr>
                        <td>{{ $pagibig->firstItem() + $index }}</td>
                        <td><span class="badge-pg">{{ number_format($row->salary_from, 2) }}</span></td>
                        <td><span class="badge-pg">{{ number_format($row->salary_to, 2) }}</span></td>
                        <td>{{ number_format($row->employee_rate, 2) }}%</td>
                        <td>{{ number_format($row->employer_rate, 2) }}%</td>
                        <td>{{ number_format($row->maximum_contribution, 2) }}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="department-action-link"
                                    onclick="openEditModal(
                                        {{ $row->pagibig_id }},
                                        '{{ $row->salary_from }}',
                                        '{{ $row->salary_to }}',
                                        '{{ $row->employee_rate }}',
                                        '{{ $row->employer_rate }}',
                                        '{{ $row->maximum_contribution }}'
                                    )">Edit</button>
                                <form action="{{ route('pagibig.destroy', $row->pagibig_id) }}" method="POST"
                                    onsubmit="return confirm('Delete this Pag-IBIG bracket?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="department-action-link delete-link">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">No Pag-IBIG brackets found. Click <strong>Add Bracket</strong> to get started.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $pagibig->links('vendor.pagination.numbers') }}
    </div>
</div>

{{-- Add Modal --}}
<div id="pagibigModal" class="modal-backdrop">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add Pag-IBIG Bracket</h3>
            <button class="btn-close" onclick="closeModal()"><i data-lucide="x"></i></button>
        </div>
        <form class="modal-form" action="{{ route('pagibig.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Salary From (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="salary_from" class="form-input" placeholder="e.g. 0.00" value="{{ old('salary_from') }}" required>
                </div>
                <div class="form-group">
                    <label>Salary To (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="salary_to" class="form-input" placeholder="e.g. 1500.00" value="{{ old('salary_to') }}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Employee Rate (%) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="employee_rate" class="form-input" placeholder="e.g. 1.00" value="{{ old('employee_rate') }}" required>
                </div>
                <div class="form-group">
                    <label>Employer Rate (%) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="employer_rate" class="form-input" placeholder="e.g. 2.00" value="{{ old('employer_rate') }}" required>
                </div>
            </div>
            <div class="form-group-full half-width">
                <label>Maximum Contribution (₱) <span class="required">*</span></label>
                <input type="number" step="0.01" name="maximum_contribution" class="form-input" placeholder="e.g. 100.00" value="{{ old('maximum_contribution') }}" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-primary">Save Bracket</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="pagibigEditModal" class="modal-backdrop">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edit Pag-IBIG Bracket</h3>
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
            <div class="form-row">
                <div class="form-group">
                    <label>Employee Rate (%) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="employee_rate" id="edit_employee_rate" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Employer Rate (%) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="employer_rate" id="edit_employer_rate" class="form-input" required>
                </div>
            </div>
            <div class="form-group-full half-width">
                <label>Maximum Contribution (₱) <span class="required">*</span></label>
                <input type="number" step="0.01" name="maximum_contribution" id="edit_maximum_contribution" class="form-input" required>
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
        document.getElementById('pagibigModal').classList.add('show');
        lucide.createIcons();
    }
    function closeModal() {
        document.getElementById('pagibigModal').classList.remove('show');
    }
    function openEditModal(id, salaryFrom, salaryTo, empRate, erRate, maxContrib) {
        const form = document.getElementById('editForm');
        form.action = `/pagibig/${id}`;
        document.getElementById('edit_salary_from').value = salaryFrom;
        document.getElementById('edit_salary_to').value = salaryTo;
        document.getElementById('edit_employee_rate').value = empRate;
        document.getElementById('edit_employer_rate').value = erRate;
        document.getElementById('edit_maximum_contribution').value = maxContrib;
        document.getElementById('pagibigEditModal').classList.add('show');
        lucide.createIcons();
    }
    function closeEditModal() {
        document.getElementById('pagibigEditModal').classList.remove('show');
    }
    window.onclick = function(e) {
        if (e.target == document.getElementById('pagibigModal')) closeModal();
        if (e.target == document.getElementById('pagibigEditModal')) closeEditModal();
    };
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => openModal());
    @endif
</script>
@endsection
