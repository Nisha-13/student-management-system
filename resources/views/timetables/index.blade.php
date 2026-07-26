@extends('layouts.app')

@section('title', 'Class Timetable - Student Management System')
@section('page-title', 'Weekly Class Schedule Grid')

@section('content')
<div id="timetableAlertContainer"></div>

<div class="card card-custom p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h5 class="fw-bold m-0"><i class="bi bi-calendar3 text-primary me-2"></i> Class Timetable Filter</h5>
        <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addSlotModal">
            <i class="bi bi-plus-lg me-1"></i> Add Period Slot
        </button>
    </div>

    <form id="timetableFilterForm" class="row g-3 align-items-end">
        <div class="col-12 col-md-5">
            <label class="form-label fw-semibold small text-muted">Class *</label>
            <select id="classFilter" class="form-select" required>
                <option value="">Select Class</option>
                @foreach($classes as $cls)
                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-5">
            <label class="form-label fw-semibold small text-muted">Section *</label>
            <select id="sectionFilter" class="form-select" required>
                <option value="">Select Section</option>
            </select>
        </div>

        <div class="col-12 col-md-2">
            <button type="submit" id="viewScheduleBtn" class="btn btn-success w-100 fw-semibold">
                <i class="bi bi-search me-1"></i> View Schedule
            </button>
        </div>
    </form>
</div>

<div id="timetableGridContainer" class="card card-custom p-4 d-none">
    <h5 class="fw-bold mb-3" id="gridTitle">Class Schedule</h5>
    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th style="width: 15%;">Day</th>
                    <th>Scheduled Periods</th>
                </tr>
            </thead>
            <tbody>
                @foreach($days as $day)
                    <tr>
                        <td class="fw-bold bg-light align-middle">{{ $day }}</td>
                        <td id="day_slot_{{ $day }}" class="p-3 text-start">
                            <span class="text-muted small">No scheduled periods.</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Add Slot -->
<div class="modal fade" id="addSlotModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addSlotForm">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Schedule Class Period</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="slotModalAlertError" class="alert alert-danger d-none mb-3"></div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold small text-muted">Class *</label>
                            <select name="school_class_id" id="modalClassSelect" class="form-select" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $cls)
                                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold small text-muted">Section *</label>
                            <select name="section_id" id="modalSectionSelect" class="form-select" required>
                                <option value="">Select Section</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold small text-muted">Subject *</label>
                            <select name="subject_id" id="modalSubjectSelect" class="form-select" required>
                                <option value="">Select Subject</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold small text-muted">Assigned Teacher *</label>
                            <select name="teacher_id" class="form-select" required>
                                <option value="">Select Teacher</option>
                                @foreach($teachers as $tch)
                                    <option value="{{ $tch->id }}">{{ $tch->user->name }} ({{ $tch->employee_id }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-4">
                            <label class="form-label fw-semibold small text-muted">Day of Week *</label>
                            <select name="day_of_week" class="form-select" required>
                                @foreach($days as $day)
                                    <option value="{{ $day }}">{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-sm-4">
                            <label class="form-label fw-semibold small text-muted">Start Time *</label>
                            <input type="time" name="start_time" class="form-control" value="09:00" required>
                        </div>
                        <div class="col-6 col-sm-4">
                            <label class="form-label fw-semibold small text-muted">End Time *</label>
                            <input type="time" name="end_time" class="form-control" value="10:00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Room / Hall Number</label>
                        <input type="text" name="room_number" class="form-control" placeholder="e.g. Room 102">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveSlotBtn" class="btn btn-primary fw-semibold">Save Schedule Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    function showAlert(message, type = 'success') {
        var alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show card-custom mb-4" role="alert">
                <i class="bi ${type === 'success' ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-danger'} me-2 fs-5"></i>
                <strong>${message}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('#timetableAlertContainer').html(alertHtml);
    }

    function loadSections(classId, sectionElem, callback) {
        sectionElem.html('<option value="">Loading...</option>');
        if (classId) {
            $.get("/classes/" + classId + "/sections", function(secs) {
                var opt = '<option value="">Select Section</option>';
                $.each(secs, function(i, s) { opt += '<option value="' + s.id + '">' + s.name + '</option>'; });
                sectionElem.html(opt);
                if (callback) callback();
            });
        } else {
            sectionElem.html('<option value="">Select Section</option>');
        }
    }

    function loadSubjects(classId, subjectElem) {
        subjectElem.html('<option value="">Loading...</option>');
        if (classId) {
            $.get("/classes/" + classId + "/subjects", function(subs) {
                var opt = '<option value="">Select Subject</option>';
                $.each(subs, function(i, s) { opt += '<option value="' + s.id + '">' + s.name + '</option>'; });
                subjectElem.html(opt);
            });
        } else {
            subjectElem.html('<option value="">Select Subject</option>');
        }
    }

    $('#classFilter').on('change', function() { loadSections($(this).val(), $('#sectionFilter')); });

    $('#modalClassSelect').on('change', function() {
        var classId = $(this).val();
        loadSections(classId, $('#modalSectionSelect'));
        loadSubjects(classId, $('#modalSubjectSelect'));
    });

    // Fetch Grid Submit
    $('#timetableFilterForm').on('submit', function(e) {
        e.preventDefault();
        var classId = $('#classFilter').val();
        var sectionId = $('#sectionFilter').val();

        if (!classId || !sectionId) {
            alert('Please select Class and Section.');
            return;
        }

        var viewBtn = $('#viewScheduleBtn');
        viewBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Loading...');

        $.post("{{ route('timetables.grid') }}", { school_class_id: classId, section_id: sectionId }, function(res) {
            viewBtn.prop('disabled', false).html('<i class="bi bi-search me-1"></i> View Schedule');
            if (res.success) {
                @foreach($days as $day)
                    $('#day_slot_{{ $day }}').html('<span class="text-muted small">No scheduled periods.</span>');
                @endforeach

                $.each(res.slots, function(i, slot) {
                    var targetTd = $('#day_slot_' + slot.day_of_week);
                    if (targetTd.find('.badge-period').length === 0) {
                        targetTd.empty();
                    }

                    var badgeHtml = `
                        <div class="badge-period card d-inline-block border-primary me-2 mb-2 p-2 shadow-sm text-start">
                            <div class="fw-bold text-primary">${slot.subject.name}</div>
                            <div class="small text-dark"><i class="bi bi-clock me-1"></i> ${slot.start_time} - ${slot.end_time}</div>
                            <div class="small text-muted"><i class="bi bi-person me-1"></i> ${slot.teacher.user.name}</div>
                            ${slot.room_number ? '<div class="small text-secondary"><i class="bi bi-geo-alt me-1"></i> ' + slot.room_number + '</div>' : ''}
                            <button type="button" class="btn btn-link btn-sm text-danger p-0 mt-1 remove-slot-btn" data-id="${slot.id}" style="font-size: 0.75rem;"><i class="bi bi-x-circle me-1"></i>Remove</button>
                        </div>
                    `;
                    targetTd.append(badgeHtml);
                });

                $('#gridTitle').text($('#classFilter option:selected').text() + ' - ' + $('#sectionFilter option:selected').text() + ' Weekly Schedule');
                $('#timetableGridContainer').removeClass('d-none');
            }
        }).fail(function() {
            viewBtn.prop('disabled', false).html('<i class="bi bi-search me-1"></i> View Schedule');
            alert('Error loading timetable grid.');
        });
    });

    // Add Slot Submit
    $('#addSlotForm').on('submit', function(e) {
        e.preventDefault();
        $('#slotModalAlertError').addClass('d-none').html('');

        var saveBtn = $('#saveSlotBtn');
        saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        $.post("{{ route('timetables.store') }}", $(this).serialize(), function(res) {
            saveBtn.prop('disabled', false).html('Save Schedule Slot');
            if (res.success) {
                $('#addSlotModal').modal('hide');
                var classId = $('#modalClassSelect').val();
                var sectionId = $('#modalSectionSelect').val();
                $('#addSlotForm')[0].reset();

                // Automatically update main filter to show the new slot
                $('#classFilter').val(classId);
                loadSections(classId, $('#sectionFilter'), function() {
                    $('#sectionFilter').val(sectionId);
                    $('#timetableFilterForm').submit();
                });

                showAlert(res.message || 'Period slot scheduled successfully!');
            }
        }).fail(function(xhr) {
            saveBtn.prop('disabled', false).html('Save Schedule Slot');
            var errorMsg = 'Failed to schedule period slot.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            $('#slotModalAlertError').removeClass('d-none').html(errorMsg);
        });
    });

    // Remove Slot
    $(document).on('click', '.remove-slot-btn', function() {
        var slotId = $(this).data('id');
        if (confirm('Remove this schedule period?')) {
            $.ajax({
                url: "/timetables/" + slotId,
                type: 'DELETE',
                success: function(res) {
                    $('#timetableFilterForm').submit();
                    showAlert('Timetable slot removed successfully.', 'warning');
                },
                error: function() {
                    alert('Error removing timetable slot.');
                }
            });
        }
    });
});
</script>
@endpush
