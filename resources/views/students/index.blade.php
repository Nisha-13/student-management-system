@extends('layouts.app')

@section('title', 'Students List - Student Management System')
@section('page-title', 'Student Management')

@section('content')
<div class="card card-custom p-3 p-md-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h5 class="fw-bold m-0">Student Directory</h5>
            <small class="text-muted">Manage all enrolled students, classes, and contact records</small>
        </div>
        <a href="{{ route('students.create') }}" class="btn btn-primary rounded-pill">
            <i class="bi bi-plus-lg me-1"></i> Add New Student
        </a>
    </div>

    <div class="table-responsive">
        <table id="studentsTable" class="table table-hover align-middle w-100">
            <thead class="table-light">
                <tr>
                    <th>Photo</th>
                    <th>Roll #</th>
                    <th>Name</th>
                    <th class="d-none d-md-table-cell">Email</th>
                    <th class="d-none d-sm-table-cell">Class</th>
                    <th class="d-none d-lg-table-cell">Section</th>
                    <th class="d-none d-lg-table-cell">Gender</th>
                    <th class="d-none d-xl-table-cell">Phone</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- JS DataTables Populated via AJAX -->
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#studentsTable').DataTable({
            processing: true,
            responsive: true,
            ajax: "{{ route('students.index') }}",
            columns: [
                {
                    data: 'avatar_url',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return '<img src="' + data + '" class="rounded-circle border" width="36" height="36" style="object-fit:cover;" alt="Avatar">';
                    }
                },
                { data: 'roll_number' },
                { data: 'name', render: function(data) { return '<span class="fw-semibold">' + data + '</span>'; } },
                { data: 'email', className: 'd-none d-md-table-cell' },
                { data: 'class_name', className: 'd-none d-sm-table-cell' },
                { data: 'section_name', className: 'd-none d-lg-table-cell' },
                { data: 'gender', className: 'd-none d-lg-table-cell' },
                { data: 'phone', className: 'd-none d-xl-table-cell' },
                { data: 'actions', orderable: false, searchable: false }
            ]
        });

        // AJAX Delete Handler
        $(document).on('click', '.delete-student-btn', function() {
            var studentId = $(this).data('id');
            if (confirm('Are you sure you want to delete this student profile?')) {
                $.ajax({
                    url: "/students/" + studentId,
                    type: 'DELETE',
                    success: function(response) {
                        alert(response.message);
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        alert('Error deleting student record.');
                    }
                });
            }
        });
    });
</script>
@endpush
