@extends('layouts.master')

@section('title', 'SSS Contributions')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/govt-contributions.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/department.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('js/admin/government-contribution/sss.js') }}"></script>
@endsection

@section('content')
<div class="govt-container">
    <div class="content-header">
        <div>
            <h2 class="header-title">SSS Contributions</h2>
            <p class="header-subtitle">Manage SSS contribution brackets</p>
        </div>
        <button class="btn-primary js-open-add-modal">
            <i data-lucide="plus"></i> Add SSS Bracket
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
                    <th>Range From (₱)</th>
                    <th>Range To (₱)</th>
                    <th>Monthly Salary Credit (₱)</th>
                    <th>Employee Share (₱)</th>
                    <th>Employer Share (₱)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sss as $index => $row)
                    <tr>
                        <td>{{ $sss->firstItem() + $index }}</td>
                        <td><span class="badge-sss">{{ number_format($row->sss_range_from, 2) }}</span></td>
                        <td><span class="badge-sss">{{ number_format($row->sss_range_to, 2) }}</span></td>
                        <td>{{ number_format($row->monthly_salary_credit, 2) }}</td>
                        <td>{{ number_format($row->employee_share, 2) }}</td>
                        <td>{{ number_format($row->employer_share, 2) }}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="department-action-link js-edit-sss"
                                    data-id="{{ $row->sss_id }}"
                                    data-range-from="{{ $row->sss_range_from }}"
                                    data-range-to="{{ $row->sss_range_to }}"
                                    data-monthly-salary-credit="{{ $row->monthly_salary_credit }}"
                                    data-employee-share="{{ $row->employee_share }}"
                                    data-employer-share="{{ $row->employer_share }}">Edit</button>
                                <form action="{{ route('sss.destroy', $row->sss_id) }}" method="POST"
                                    onsubmit="return confirm('Delete this SSS bracket?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="department-action-link delete-link">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">No SSS brackets found. Click <strong>Add SSS Bracket</strong> to get started.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $sss->links('vendor.pagination.numbers') }}
    </div>
</div>

{{-- Add Modal --}}
<div id="sssModal" class="modal-backdrop">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add SSS Bracket</h3>
            <button class="btn-close js-close-add-modal"><i data-lucide="x"></i></button>
        </div>
        <form class="modal-form" action="{{ route('sss.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Range From (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="sss_range_from" class="form-input" placeholder="e.g. 0.00" value="{{ old('sss_range_from') }}" required>
                </div>
                <div class="form-group">
                    <label>Range To (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="sss_range_to" class="form-input" placeholder="e.g. 4999.99" value="{{ old('sss_range_to') }}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Monthly Salary Credit (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="monthly_salary_credit" class="form-input" placeholder="e.g. 5000.00" value="{{ old('monthly_salary_credit') }}" required>
                </div>
                <div class="form-group">
                    <label>Employee Share (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="employee_share" class="form-input" placeholder="e.g. 225.00" value="{{ old('employee_share') }}" required>
                </div>
            </div>
            <div class="form-group-full half-width">
                <label>Employer Share (₱) <span class="required">*</span></label>
                <input type="number" step="0.01" name="employer_share" class="form-input" placeholder="e.g. 461.50" value="{{ old('employer_share') }}" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary js-close-add-modal">Cancel</button>
                <button type="submit" class="btn-primary">Save Bracket</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="sssEditModal" class="modal-backdrop">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edit SSS Bracket</h3>
            <button class="btn-close js-close-edit-modal"><i data-lucide="x"></i></button>
        </div>
        <form class="modal-form" id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label>Range From (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="sss_range_from" id="edit_sss_range_from" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Range To (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="sss_range_to" id="edit_sss_range_to" class="form-input" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Monthly Salary Credit (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="monthly_salary_credit" id="edit_monthly_salary_credit" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Employee Share (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="employee_share" id="edit_employee_share" class="form-input" required>
                </div>
            </div>
            <div class="form-group-full half-width">
                <label>Employer Share (₱) <span class="required">*</span></label>
                <input type="number" step="0.01" name="employer_share" id="edit_employer_share" class="form-input" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary js-close-edit-modal">Cancel</button>
                <button type="submit" class="btn-primary">Update Bracket</button>
            </div>
        </form>
    </div>
</div>
@endsection

