@extends('layouts.master')

@section('title', 'Tax Brackets')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/govt-contributions.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/department.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('js/admin/government-contribution/tax.js') }}"></script>
@endsection

@section('content')
<div class="govt-container">
    <div class="content-header">
        <div>
            <h2 class="header-title">Tax Brackets</h2>
            <p class="header-subtitle">Manage withholding tax brackets</p>
        </div>
        <button class="btn-primary js-open-add-modal">
            <i data-lucide="plus"></i> Add Tax Bracket
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
                    <th>Base Tax (₱)</th>
                    <th>Tax Rate (%)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($taxes as $index => $row)
                    <tr>
                        <td>{{ $taxes->firstItem() + $index }}</td>
                        <td><span class="badge-tax">{{ number_format($row->salary_from, 2) }}</span></td>
                        <td><span class="badge-tax">{{ number_format($row->salary_to, 2) }}</span></td>
                        <td>{{ number_format($row->base_tax, 2) }}</td>
                        <td>{{ number_format($row->tax_rate, 2) }}%</td>
                        <td>
                            <div class="action-buttons">
                                <button class="department-action-link js-edit-tax"
                                    data-id="{{ $row->tax_id }}"
                                    data-salary-from="{{ $row->salary_from }}"
                                    data-salary-to="{{ $row->salary_to }}"
                                    data-base-tax="{{ $row->base_tax }}"
                                    data-tax-rate="{{ $row->tax_rate }}">Edit</button>
                                <form action="{{ route('tax.destroy', $row->tax_id) }}" method="POST"
                                    onsubmit="return confirm('Delete this tax bracket?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="department-action-link delete-link">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">No tax brackets found. Click <strong>Add Tax Bracket</strong> to get started.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $taxes->links('vendor.pagination.numbers') }}
    </div>
</div>

{{-- Add Modal --}}
<div id="taxModal" class="modal-backdrop">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add Tax Bracket</h3>
            <button class="btn-close js-close-add-modal"><i data-lucide="x"></i></button>
        </div>
        <form class="modal-form" action="{{ route('tax.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Salary From (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="salary_from" class="form-input" placeholder="e.g. 0.00" value="{{ old('salary_from') }}" required>
                </div>
                <div class="form-group">
                    <label>Salary To (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="salary_to" class="form-input" placeholder="e.g. 20833.00" value="{{ old('salary_to') }}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Base Tax (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="base_tax" class="form-input" placeholder="e.g. 0.00" value="{{ old('base_tax') }}" required>
                </div>
                <div class="form-group">
                    <label>Tax Rate (%) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="tax_rate" class="form-input" placeholder="e.g. 20.00" value="{{ old('tax_rate') }}" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary js-close-add-modal">Cancel</button>
                <button type="submit" class="btn-primary">Save Bracket</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="taxEditModal" class="modal-backdrop">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edit Tax Bracket</h3>
            <button class="btn-close js-close-edit-modal"><i data-lucide="x"></i></button>
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
                    <label>Base Tax (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="base_tax" id="edit_base_tax" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Tax Rate (%) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="tax_rate" id="edit_tax_rate" class="form-input" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary js-close-edit-modal">Cancel</button>
                <button type="submit" class="btn-primary">Update Bracket</button>
            </div>
        </form>
    </div>
</div>
@endsection

