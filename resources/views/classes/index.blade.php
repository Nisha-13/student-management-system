@extends('layouts.app')

@section('title', 'Class Management - Student Management System')
@section('page-title', 'Classes & Sections Management')

@section('content')
<div class="row g-4">
    {{-- Classes Table --}}
    <div class="col-lg-8">
        <div class="card card-custom p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h5 class="fw-bold m-0">All Classes</h5>
                    <small class="text-muted">Manage grade levels / classes</small>
                </div>
                <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#createClassModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Class
                </button>
            </div>
            <div class="table-responsive">
                <table id="classesTable" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Class Name</th>
                            <th class="d-none d-sm-table-cell">Code</th>
                            <th class="text-center">Sections</th>
                            <th class="text-center d-none d-md-table-cell">Students</th>
                            <th class="text-center d-none d-lg-table-cell">Subjects</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sections Panel --}}
    <div class="col-lg-4">
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-diagram-3 text-primary me-2"></i> Manage Sections</h5>
            <div class="mb-3">
                <label class="form-label fw-semibold small text-muted">Select Class to Manage Sections</label>
                <select id="classSectionFilter" class="form-select">
                    <option value="">Choose Class...</option>
                </select>
            </div>

            <form id="addSectionForm" class="d-none">
                <div class="input-group mb-3">
                    <input type="text" id="sectionNameInput" class="form-control" placeholder="Section name (e.g. Section A)">
                    <input type="number" id="sectionCapacity" class="form-control" placeholder="Capacity" style="max-width:100px;" value="40">
                    <button type="submit" class="btn btn-success"><i class="bi bi-plus-lg"></i></button>
                </div>
            </form>

            <ul id="sectionList" class="list-group">
                <li class="list-group-item text-muted text-center small">Select a class above to view sections</li>
            </ul>
        </div>
    </div>
</div>

{{-- Modal: Create Class --}}
<div class="modal fade" id="createClassModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="createClassForm">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Class Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Grade 10" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Class Code</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. 10th">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-semibold">Save Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Edit Class --}}
<div class="modal fade" id="editClassModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="editClassForm">
                <input type="hidden" id="editClassId">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Class Name *</label>
                        <input type="text" id="editClassName" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Class Code</label>
                        <input type="text" id="editClassCode" name="code" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-semibold">Update Class</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    var classTable = $('#classesTable').DataTable({
        processing: true,
        ajax: { url: "{{ route('admin.classes.index') }}", dataSrc: 'data' },
        columns: [
            { data: 'name', render: function(d) { return '<span class="fw-semibold">' + d + '</span>'; } },
            { data: 'code', className: 'd-none d-sm-table-cell' },
            { data: 'sections_count', className: 'text-center' },
            { data: 'students_count', className: 'text-center d-none d-md-table-cell' },
            { data: 'subjects_count', className: 'text-center d-none d-lg-table-cell' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-center' },
        ]
    });

    function reloadClassDropdown() {
        $.get("{{ route('admin.classes.index') }}", function(res) {
            var options = '<option value="">Choose Class...</option>';
            $.each(res.data, function(i, c) {
                options += '<option value="' + c.id + '">' + c.name + '</option>';
            });
            $('#classSectionFilter').html(options);
        });
    }

    function reloadSections(classId) {
        $.get("/admin/classes/" + classId + "/sections-manage", function(sections) {
            var html = '';
            if (sections.length === 0) {
                html = '<li class="list-group-item text-muted text-center small">No sections yet. Add one above.</li>';
            } else {
                $.each(sections, function(i, s) {
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-diagram-3 me-2 text-primary"></i> <strong>${s.name}</strong> <small class="text-muted">(Cap: ${s.capacity} | Students: ${s.students_count})</small></span>
                        <button class="btn btn-sm btn-outline-danger delete-section-btn" data-id="${s.id}"><i class="bi bi-trash"></i></button>
                    </li>`;
                });
            }
            $('#sectionList').html(html);
        });
    }

    $('#classSectionFilter').on('change', function() {
        var classId = $(this).val();
        if (classId) {
            $('#addSectionForm').removeClass('d-none');
            reloadSections(classId);
        } else {
            $('#addSectionForm').addClass('d-none');
            $('#sectionList').html('<li class="list-group-item text-muted text-center small">Select a class above</li>');
        }
    });

    $('#addSectionForm').on('submit', function(e) {
        e.preventDefault();
        var classId = $('#classSectionFilter').val();
        $.post("/admin/classes/" + classId + "/sections", {
            name: $('#sectionNameInput').val(),
            capacity: $('#sectionCapacity').val()
        }, function(res) {
            if (res.success) {
                $('#sectionNameInput').val('');
                reloadSections(classId);
                classTable.ajax.reload(null, false);
                reloadClassDropdown();
            }
        }).fail(function(xhr) { alert(xhr.responseJSON?.message || 'Error adding section.'); });
    });

    $(document).on('click', '.delete-section-btn', function() {
        var id = $(this).data('id');
        if (confirm('Delete this section?')) {
            $.ajax({ url: "/admin/sections/" + id, type: 'DELETE',
                success: function(res) {
                    reloadSections($('#classSectionFilter').val());
                    classTable.ajax.reload(null, false);
                },
                error: function(xhr) { alert(xhr.responseJSON?.message || 'Error deleting section.'); }
            });
        }
    });

    $('#createClassForm').on('submit', function(e) {
        e.preventDefault();
        $.post("{{ route('admin.classes.store') }}", $(this).serialize(), function(res) {
            if (res.success) {
                $('#createClassModal').modal('hide');
                $('#createClassForm')[0].reset();
                classTable.ajax.reload(null, false);
                reloadClassDropdown();
            }
        }).fail(function(xhr) { alert(xhr.responseJSON?.message || 'Validation error.'); });
    });

    $(document).on('click', '.edit-class-btn', function() {
        $('#editClassId').val($(this).data('id'));
        $('#editClassName').val($(this).data('name'));
        $('#editClassCode').val($(this).data('code'));
        $('#editClassModal').modal('show');
    });

    $('#editClassForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#editClassId').val();
        $.ajax({ url: "/admin/classes/" + id, type: 'PUT', data: $(this).serialize(),
            success: function(res) {
                if (res.success) {
                    $('#editClassModal').modal('hide');
                    classTable.ajax.reload(null, false);
                    reloadClassDropdown();
                }
            },
            error: function(xhr) { alert(xhr.responseJSON?.message || 'Error updating class.'); }
        });
    });

    $(document).on('click', '.delete-class-btn', function() {
        var id = $(this).data('id');
        if (confirm('Delete this class? All related data must be cleared first.')) {
            $.ajax({ url: "/admin/classes/" + id, type: 'DELETE',
                success: function(res) { classTable.ajax.reload(null, false); reloadClassDropdown(); },
                error: function(xhr) { alert(xhr.responseJSON?.message || 'Cannot delete class.'); }
            });
        }
    });

    reloadClassDropdown();
});
</script>
@endpush
