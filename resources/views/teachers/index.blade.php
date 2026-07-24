@extends('layouts.app')

@section('title', 'Teachers List - Student Management System')
@section('page-title', 'Teacher Management')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold m-0">Faculty Directory</h5>
            <small class="text-muted">Manage all teachers and academic staff members</small>
        </div>
        <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary rounded-pill">
            <i class="bi bi-plus-lg me-1"></i> Add New Teacher
        </a>
    </div>

    <div class="table-responsive">
        <table id="teachersTable" class="table table-hover align-middle w-100">
            <thead class="table-light">
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Qualification</th>
                    <th>Phone</th>
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
        var table = $('#teachersTable').DataTable({
            processing: true,
            ajax: "{{ route('admin.teachers.index') }}",
            columns: [
                { data: 'employee_id' },
                { data: 'name', render: function(data) { return '<span class="fw-semibold">' + data + '</span>'; } },
                { data: 'email' },
                { data: 'qualification' },
                { data: 'phone' },
                { data: 'actions', orderable: false, searchable: false }
            ]
        });

        // AJAX Delete Handler
        $(document).on('click', '.delete-teacher-btn', function() {
            var teacherId = $(this).data('id');
            if (confirm('Are you sure you want to delete this teacher record?')) {
                $.ajax({
                    url: "/admin/teachers/" + teacherId,
                    type: 'DELETE',
                    success: function(response) {
                        alert(response.message);
                        table.ajax.reload(null, false);
                    },
                    error: function() {
                        alert('Error deleting teacher record.');
                    }
                });
            }
        });
    });
</script>
@endpush
