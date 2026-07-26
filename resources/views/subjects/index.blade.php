@extends('layouts.app')

@section('title', 'Subject Management - Student Management System')
@section('page-title', 'Subjects Management')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h5 class="fw-bold m-0">All Subjects</h5>
            <small class="text-muted">Manage subjects assigned to each class</small>
        </div>
        <button class="btn btn-success rounded-pill" data-bs-toggle="modal" data-bs-target="#createSubjectModal">
            <i class="bi bi-plus-lg me-1"></i> Add Subject
        </button>
    </div>
    <div class="table-responsive">
        <table id="subjectsTable" class="table table-hover align-middle w-100">
            <thead class="table-light">
                <tr>
                    <th>Subject Name</th>
                    <th class="d-none d-sm-table-cell">Subject Code</th>
                    <th>Class</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- Modal: Create Subject --}}
<div class="modal fade" id="createSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createSubjectForm">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Subject Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Mathematics" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Subject Code *</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. MATH10" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Assigned Class *</label>
                        <select name="school_class_id" class="form-select" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $cls)
                                <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-semibold">Save Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Edit Subject --}}
<div class="modal fade" id="editSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editSubjectForm">
                <input type="hidden" id="editSubjectId">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Subject Name *</label>
                        <input type="text" id="editSubjectName" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Subject Code *</label>
                        <input type="text" id="editSubjectCode" name="code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Assigned Class *</label>
                        <select id="editSubjectClass" name="school_class_id" class="form-select" required>
                            @foreach($classes as $cls)
                                <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-semibold">Update Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#subjectsTable').DataTable({
        processing: true,
        ajax: { url: "{{ route('admin.subjects.index') }}", dataSrc: 'data' },
        columns: [
            { data: 'name', render: function(d) { return '<span class="fw-semibold">' + d + '</span>'; } },
            { data: 'code', render: function(d) { return '<code class="bg-light px-2 py-1 rounded">' + d + '</code>'; }, className: 'd-none d-sm-table-cell' },
            { data: 'class_name' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-center' },
        ]
    });

    $('#createSubjectForm').on('submit', function(e) {
        e.preventDefault();
        $.post("{{ route('admin.subjects.store') }}", $(this).serialize(), function(res) {
            if (res.success) {
                $('#createSubjectModal').modal('hide');
                $('#createSubjectForm')[0].reset();
                table.ajax.reload(null, false);
            }
        }).fail(function(xhr) { alert(xhr.responseJSON?.message || 'Validation error.'); });
    });

    $(document).on('click', '.edit-subject-btn', function() {
        $('#editSubjectId').val($(this).data('id'));
        $('#editSubjectName').val($(this).data('name'));
        $('#editSubjectCode').val($(this).data('code'));
        $('#editSubjectClass').val($(this).data('class'));
        $('#editSubjectModal').modal('show');
    });

    $('#editSubjectForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#editSubjectId').val();
        $.ajax({ url: "/admin/subjects/" + id, type: 'PUT', data: $(this).serialize(),
            success: function(res) {
                if (res.success) { $('#editSubjectModal').modal('hide'); table.ajax.reload(null, false); }
            },
            error: function(xhr) { alert(xhr.responseJSON?.message || 'Error updating subject.'); }
        });
    });

    $(document).on('click', '.delete-subject-btn', function() {
        var id = $(this).data('id');
        if (confirm('Delete this subject?')) {
            $.ajax({ url: "/admin/subjects/" + id, type: 'DELETE',
                success: function(res) { table.ajax.reload(null, false); },
                error: function(xhr) { alert(xhr.responseJSON?.message || 'Cannot delete subject.'); }
            });
        }
    });
});
</script>
@endpush
