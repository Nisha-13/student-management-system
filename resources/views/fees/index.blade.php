@extends('layouts.app')

@section('title', 'Fee Management - Student Management System')
@section('page-title', 'Fee Collection & Dues')

@section('content')
<div id="feeAlertContainer"></div>

<div class="card card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold m-0">Fee Management</h5>
            <small class="text-muted">Generate fee demands, track payments, and review balances</small>
        </div>
        <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#createFeeModal">
            <i class="bi bi-plus-lg me-1"></i> Generate Fee Demand
        </button>
    </div>

    <div class="table-responsive">
        <table id="feesTable" class="table table-hover align-middle w-100">
            <thead class="table-light">
                <tr>
                    <th>Roll #</th>
                    <th>Student Name</th>
                    <th>Class / Section</th>
                    <th>Title</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Due Balance</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal: Generate Fee Demand -->
<div class="modal fade" id="createFeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createFeeForm">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Generate Student Fee Demand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="modalAlertError" class="alert alert-danger d-none mb-3"></div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Select Student *</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">Choose Student...</option>
                            @foreach($classes as $cls)
                                @foreach($cls->students as $stu)
                                    <option value="{{ $stu->id }}">{{ $stu->user->name }} (Roll: {{ $stu->roll_number }} - {{ $cls->name }})</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Fee Description / Title *</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Q1 Tuition Fee" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Amount ($) *</label>
                            <input type="number" step="0.01" min="0" name="amount" class="form-control" placeholder="500.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Due Date *</label>
                            <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Remarks</label>
                        <input type="text" name="remarks" class="form-control" placeholder="Optional notes">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveFeeBtn" class="btn btn-primary fw-semibold">Generate Demand</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Record Payment -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="paymentForm">
                <input type="hidden" id="payFeeId">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Record Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Total Paid Amount ($)</label>
                        <input type="number" step="0.01" min="0" id="payAmountInput" class="form-control" required>
                        <small class="text-muted d-block mt-1">Total Fee Amount: $<span id="payTotalLabel"></span></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="savePayBtn" class="btn btn-success fw-semibold">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#feesTable').DataTable({
        processing: true,
        ajax: "{{ route('fees.index') }}",
        columns: [
            { data: 'roll_number' },
            { data: 'student_name', render: function(d) { return '<span class="fw-semibold">' + d + '</span>'; } },
            { data: 'class_section' },
            { data: 'title' },
            { data: 'amount' },
            { data: 'paid_amount' },
            { data: 'due_balance', render: function(d) { return '<span class="text-danger fw-semibold">' + d + '</span>'; } },
            { data: 'due_date' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    function showAlert(message, type = 'success') {
        var alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show card-custom mb-4" role="alert">
                <i class="bi ${type === 'success' ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-danger'} me-2 fs-5"></i>
                <strong>${message}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('#feeAlertContainer').html(alertHtml);
    }

    // Create Fee Submit
    $('#createFeeForm').on('submit', function(e) {
        e.preventDefault();
        $('#modalAlertError').addClass('d-none').html('');
        var saveBtn = $('#saveFeeBtn');
        saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

        $.post("{{ route('fees.store') }}", $(this).serialize(), function(res) {
            saveBtn.prop('disabled', false).html('Generate Demand');
            if (res.success) {
                $('#createFeeModal').modal('hide');
                $('#createFeeForm')[0].reset();
                table.ajax.reload(null, false);
                showAlert(res.message || 'Fee demand generated successfully!');
            }
        }).fail(function(xhr) {
            saveBtn.prop('disabled', false).html('Generate Demand');
            var errorMsg = 'Failed to generate fee demand.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            $('#modalAlertError').removeClass('d-none').html(errorMsg);
        });
    });

    // Edit Payment Click
    $(document).on('click', '.edit-payment-btn', function() {
        var id = $(this).data('id');
        var amount = $(this).data('amount');
        var paid = $(this).data('paid');

        $('#payFeeId').val(id);
        $('#payAmountInput').val(paid).attr('max', amount);
        $('#payTotalLabel').text(amount);
        $('#paymentModal').modal('show');
    });

    // Save Payment Submit
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#payFeeId').val();
        var paid = $('#payAmountInput').val();
        var saveBtn = $('#savePayBtn');
        saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        $.ajax({
            url: "/fees/" + id + "/payment",
            type: 'PUT',
            data: { paid_amount: paid },
            success: function(res) {
                saveBtn.prop('disabled', false).html('Save Payment');
                if (res.success) {
                    $('#paymentModal').modal('hide');
                    table.ajax.reload(null, false);
                    showAlert(res.message || 'Payment recorded successfully!');
                }
            },
            error: function(xhr) {
                saveBtn.prop('disabled', false).html('Save Payment');
                alert(xhr.responseJSON?.message || 'Error recording payment.');
            }
        });
    });

    // Delete Fee
    $(document).on('click', '.delete-fee-btn', function() {
        var id = $(this).data('id');
        if (confirm('Delete this fee demand record?')) {
            $.ajax({
                url: "/fees/" + id,
                type: 'DELETE',
                success: function(res) {
                    table.ajax.reload(null, false);
                    showAlert('Fee record deleted successfully.', 'warning');
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Error deleting fee record.');
                }
            });
        }
    });
});
</script>
@endpush
