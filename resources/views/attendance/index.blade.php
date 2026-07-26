@extends('layouts.app')

@section('title', 'Attendance Register - Student Management System')
@section('page-title', 'Daily Attendance Register')

@section('content')
<div class="card card-custom p-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-funnel text-primary me-2"></i> Select Class & Date</h5>

    <form id="attendanceFilterForm" class="row g-3 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label fw-semibold small text-muted">Class *</label>
            <select id="classFilter" class="form-select" required>
                <option value="">Select Class</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-4">
            <label class="form-label fw-semibold small text-muted">Section *</label>
            <select id="sectionFilter" class="form-select" required>
                <option value="">Select Section</option>
            </select>
        </div>

        <div class="col-12 col-md-4">
            <label class="form-label fw-semibold small text-muted">Date *</label>
            <div class="input-group">
                <input type="date" id="dateFilter" class="form-select" value="{{ date('Y-m-d') }}" required>
                <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-search me-1"></i> Fetch Roster</button>
            </div>
        </div>
    </form>
</div>

<div id="attendanceContainer" class="card card-custom p-4 d-none">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold m-0">Student Attendance Sheet</h5>
            <small class="text-muted" id="sheetSubtitle">Class Roster</small>
        </div>
        <button type="button" id="markAllPresent" class="btn btn-outline-success btn-sm rounded-pill">
            <i class="bi bi-check-all me-1"></i> Mark All Present
        </button>
    </div>

    <form id="attendanceSubmitForm">
        <div class="table-responsive mb-4">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Roll #</th>
                        <th>Student Name</th>
                        <th class="attendance-status-group">Attendance Status</th>
                        <th class="d-none d-sm-table-cell">Remarks</th>
                    </tr>
                </thead>
                <tbody id="attendanceTbody">
                    <!-- Dynamic AJAX Roster Rows -->
                </tbody>
            </table>
        </div>

        <div class="text-end">
            <button type="submit" id="saveAttendanceBtn" class="btn btn-success px-4 py-2 fw-semibold">
                <i class="bi bi-save me-1"></i> Save Attendance
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // Dynamic Section Population on Class Selection
    $('#classFilter').on('change', function() {
        var classId = $(this).val();
        var sectionFilter = $('#sectionFilter');
        sectionFilter.html('<option value="">Loading sections...</option>');

        if (classId) {
            $.ajax({
                url: "/classes/" + classId + "/sections",
                type: 'GET',
                success: function(sections) {
                    var options = '<option value="">Select Section</option>';
                    $.each(sections, function(i, sec) {
                        options += '<option value="' + sec.id + '">' + sec.name + '</option>';
                    });
                    sectionFilter.html(options);
                }
            });
        } else {
            sectionFilter.html('<option value="">Select Section</option>');
        }
    });

    // Fetch Roster via AJAX
    $('#attendanceFilterForm').on('submit', function(e) {
        e.preventDefault();

        var classId = $('#classFilter').val();
        var sectionId = $('#sectionFilter').val();
        var attDate = $('#dateFilter').val();

        if (!classId || !sectionId || !attDate) {
            alert('Please select Class, Section, and Date.');
            return;
        }

        $.ajax({
            url: "{{ route('attendance.fetch') }}",
            type: 'POST',
            data: {
                school_class_id: classId,
                section_id: sectionId,
                date: attDate
            },
            success: function(response) {
                if (response.success) {
                    var tbody = $('#attendanceTbody');
                    tbody.empty();

                    if (response.students.length === 0) {
                        tbody.html('<tr><td colspan="4" class="text-center text-muted py-4">No enrolled students found for this class and section.</td></tr>');
                    } else {
                        $.each(response.students, function(index, student) {
                            var row = `
                                <tr>
                                    <td class="fw-semibold">${student.roll_number}</td>
                                    <td>
                                        <input type="hidden" name="attendance[${index}][student_id]" value="${student.student_id}">
                                        <span class="fw-semibold">${student.name}</span>
                                    </td>
                                    <td class="attendance-status-group">
                                        <div class="btn-group" role="group">
                                            <input type="radio" class="btn-check" name="attendance[${index}][status]" id="p_${student.student_id}" value="present" ${student.status === 'present' ? 'checked' : ''}>
                                            <label class="btn btn-outline-success btn-sm" for="p_${student.student_id}">Present</label>

                                            <input type="radio" class="btn-check" name="attendance[${index}][status]" id="a_${student.student_id}" value="absent" ${student.status === 'absent' ? 'checked' : ''}>
                                            <label class="btn btn-outline-danger btn-sm" for="a_${student.student_id}">Absent</label>

                                            <input type="radio" class="btn-check" name="attendance[${index}][status]" id="l_${student.student_id}" value="late" ${student.status === 'late' ? 'checked' : ''}>
                                            <label class="btn btn-outline-warning btn-sm" for="l_${student.student_id}">Late</label>

                                            <input type="radio" class="btn-check" name="attendance[${index}][status]" id="e_${student.student_id}" value="excused" ${student.status === 'excused' ? 'checked' : ''}>
                                            <label class="btn btn-outline-info btn-sm" for="e_${student.student_id}">Excused</label>
                                        </div>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <input type="text" name="attendance[${index}][remarks]" class="form-control form-control-sm" placeholder="Optional remark" value="${student.remarks || ''}">
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                    }

                    $('#sheetSubtitle').text($('#classFilter option:selected').text() + ' - ' + $('#sectionFilter option:selected').text() + ' (' + attDate + ')');
                    $('#attendanceContainer').removeClass('d-none');
                }
            },
            error: function() {
                alert('Error loading student roster.');
            }
        });
    });

    // Mark All Present Shortcut
    $('#markAllPresent').on('click', function() {
        $('input[type="radio"][value="present"]').prop('checked', true);
    });

    // Save Attendance via AJAX
    $('#attendanceSubmitForm').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serializeArray();
        formData.push({ name: 'school_class_id', value: $('#classFilter').val() });
        formData.push({ name: 'section_id', value: $('#sectionFilter').val() });
        formData.push({ name: 'date', value: $('#dateFilter').val() });

        var saveBtn = $('#saveAttendanceBtn');
        saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        $.ajax({
            url: "{{ route('attendance.store') }}",
            type: 'POST',
            data: $.param(formData),
            success: function(response) {
                saveBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save Attendance');
                if (response.success) {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                saveBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save Attendance');
                alert('Failed to save attendance records. Please verify entries.');
            }
        });
    });
});
</script>
@endpush
