@extends('layouts.app')

@section('title', 'Marks Entry - Student Management System')
@section('page-title', 'Examination Marks Management')

@section('content')
<div class="card card-custom p-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-funnel text-success me-2"></i> Select Class, Subject & Exam</h5>

    <form id="marksFilterForm" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-semibold small text-muted">Class *</label>
            <select id="classFilter" class="form-select" required>
                <option value="">Select Class</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold small text-muted">Subject *</label>
            <select id="subjectFilter" class="form-select" required>
                <option value="">Select Subject</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold small text-muted">Exam Title *</label>
            <div class="input-group">
                <input type="text" id="examFilter" class="form-control" placeholder="e.g. Midterm 2026" value="Midterm Exam" required>
                <button type="submit" class="btn btn-success fw-semibold"><i class="bi bi-search me-1"></i> Load Students</button>
            </div>
        </div>
    </form>
</div>

<div id="marksContainer" class="card card-custom p-4 d-none">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold m-0">Marks Entry Sheet</h5>
            <small class="text-muted" id="marksSubtitle">Subject Score Sheet</small>
        </div>
    </div>

    <form id="marksSubmitForm">
        <div class="table-responsive mb-4">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 15%;">Roll #</th>
                        <th style="width: 25%;">Student Name</th>
                        <th style="width: 20%;">Marks Obtained *</th>
                        <th style="width: 20%;">Max Marks *</th>
                        <th style="width: 20%;">Remarks</th>
                    </tr>
                </thead>
                <tbody id="marksTbody">
                    <!-- Dynamic AJAX Rows -->
                </tbody>
            </table>
        </div>

        <div class="text-end">
            <button type="submit" id="saveMarksBtn" class="btn btn-success px-4 py-2 fw-semibold">
                <i class="bi bi-save me-1"></i> Save All Marks
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // Dynamic Subject Fetching
    $('#classFilter').on('change', function() {
        var classId = $(this).val();
        var subjectFilter = $('#subjectFilter');
        subjectFilter.html('<option value="">Loading subjects...</option>');

        if (classId) {
            $.ajax({
                url: "/classes/" + classId + "/subjects",
                type: 'GET',
                success: function(subjects) {
                    var options = '<option value="">Select Subject</option>';
                    $.each(subjects, function(i, sub) {
                        options += '<option value="' + sub.id + '">' + sub.name + ' (' + sub.code + ')</option>';
                    });
                    subjectFilter.html(options);
                }
            });
        } else {
            subjectFilter.html('<option value="">Select Subject</option>');
        }
    });

    // Fetch Student Marks Roster
    $('#marksFilterForm').on('submit', function(e) {
        e.preventDefault();

        var classId = $('#classFilter').val();
        var subjectId = $('#subjectFilter').val();
        var examName = $('#examFilter').val();

        if (!classId || !subjectId || !examName) {
            alert('Please select Class, Subject, and enter Exam Title.');
            return;
        }

        $.ajax({
            url: "{{ route('marks.fetch') }}",
            type: 'POST',
            data: {
                school_class_id: classId,
                subject_id: subjectId,
                exam_name: examName
            },
            success: function(response) {
                if (response.success) {
                    var tbody = $('#marksTbody');
                    tbody.empty();

                    if (response.students.length === 0) {
                        tbody.html('<tr><td colspan="5" class="text-center text-muted py-4">No students found for this class.</td></tr>');
                    } else {
                        $.each(response.students, function(index, student) {
                            var maxVal = student.max_marks || 100;
                            var row = `
                                <tr>
                                    <td class="fw-semibold">${student.roll_number}</td>
                                    <td>
                                        <input type="hidden" name="marks[${index}][student_id]" value="${student.student_id}">
                                        <span class="fw-semibold">${student.name}</span>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" max="${maxVal}" name="marks[${index}][marks_obtained]" class="form-control marks-obtained-input" value="${student.marks_obtained}" placeholder="e.g. 85.5" required>
                                        <div class="invalid-feedback small">Must not exceed max marks</div>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="1" name="marks[${index}][max_marks]" class="form-control max-marks-input" value="${maxVal}" required>
                                    </td>
                                    <td>
                                        <input type="text" name="marks[${index}][remarks]" class="form-control" placeholder="Optional remark" value="${student.remarks || ''}">
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                    }

                    $('#marksSubtitle').text($('#classFilter option:selected').text() + ' - ' + $('#subjectFilter option:selected').text() + ' (' + examName + ')');
                    $('#marksContainer').removeClass('d-none');
                }
            },
            error: function() {
                alert('Error loading students marks roster.');
            }
        });
    });

    // Real-time Validation: Obtained Marks <= Max Marks
    $(document).on('input change', '.marks-obtained-input, .max-marks-input', function() {
        var row = $(this).closest('tr');
        var obtainedInput = row.find('.marks-obtained-input');
        var maxInput = row.find('.max-marks-input');

        var obtained = parseFloat(obtainedInput.val());
        var max = parseFloat(maxInput.val());

        // Update HTML max attribute
        if (!isNaN(max) && max > 0) {
            obtainedInput.attr('max', max);
        }

        if (!isNaN(obtained) && !isNaN(max) && obtained > max) {
            obtainedInput.addClass('is-invalid');
        } else {
            obtainedInput.removeClass('is-invalid');
        }
    });

    // Save Marks via AJAX with strict validation check
    $('#marksSubmitForm').on('submit', function(e) {
        e.preventDefault();

        var hasInvalid = false;
        $('#marksTbody tr').each(function() {
            var obtainedInput = $(this).find('.marks-obtained-input');
            var maxInput = $(this).find('.max-marks-input');
            var obtained = parseFloat(obtainedInput.val());
            var max = parseFloat(maxInput.val());

            if (!isNaN(obtained) && !isNaN(max) && obtained > max) {
                obtainedInput.addClass('is-invalid');
                hasInvalid = true;
            }
        });

        if (hasInvalid) {
            alert('Validation Error: Obtained marks cannot be greater than Maximum marks.');
            return false;
        }

        var formData = $(this).serializeArray();
        formData.push({ name: 'school_class_id', value: $('#classFilter').val() });
        formData.push({ name: 'subject_id', value: $('#subjectFilter').val() });
        formData.push({ name: 'exam_name', value: $('#examFilter').val() });

        var saveBtn = $('#saveMarksBtn');
        saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        $.ajax({
            url: "{{ route('marks.store') }}",
            type: 'POST',
            data: $.param(formData),
            success: function(response) {
                saveBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save All Marks');
                if (response.success) {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                saveBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save All Marks');
                var msg = 'Failed to save marks records.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = Object.values(xhr.responseJSON.errors).flat();
                    msg = errors.join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
            }
        });
    });
});
</script>
@endpush
