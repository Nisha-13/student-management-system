@extends('layouts.app')

@section('title', 'Students List - Student Management System')
@section('page-title', 'Student Management')

@section('content')

<div class="card card-custom shadow-sm border-0">

    <div class="card-body p-3 p-md-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

            <div>

                <h4 class="fw-bold mb-1">
                    Student Directory
                </h4>

                <small class="text-muted">
                    Manage all enrolled students, classes and contact records
                </small>

            </div>

            <a href="{{ route('students.create') }}"
               class="btn btn-primary rounded-pill px-4 add-student-btn">

                <i class="bi bi-plus-lg me-1"></i>

                Add New Student

            </a>

        </div>

        <div class="table-responsive students-table-wrapper">

            <table
                id="studentsTable"
                class="table table-hover align-middle w-100">

                <thead class="table-light">

                    <tr>

                        <th>Photo</th>

                        <th>Roll #</th>

                        <th>Name</th>

                        <th>Email</th>

                        <th>Class</th>

                        <th>Section</th>

                        <th>Gender</th>

                        <th>Phone</th>

                        <th class="text-center">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection


@push('styles')

<style>

.card-custom{

    border-radius:16px;

}

/* Button */

.add-student-btn{

    white-space:nowrap;

}

@media(max-width:767px){

.add-student-btn{

    width:100%;

}

}

/* Table */

.students-table-wrapper{

    overflow-x:auto;

    overflow-y:hidden;

    -webkit-overflow-scrolling:touch;

}

#studentsTable{

    width:100%!important;

    min-width:1100px;

    table-layout:auto;

}

#studentsTable thead th{

    font-weight:600;

    white-space:nowrap;

}

#studentsTable td{

    white-space:nowrap;

    vertical-align:middle;

}

/* Avatar */

.student-avatar{

    width:38px;

    height:38px;

    border-radius:50%;

    object-fit:cover;

}

/* Action column */

#studentsTable th:last-child,
#studentsTable td:last-child{

    white-space:nowrap;

    text-align:center;

    min-width:140px;

}

/* Scrollbar */

.students-table-wrapper::-webkit-scrollbar{

    height:8px;

}

.students-table-wrapper::-webkit-scrollbar-thumb{

    background:#c8c8c8;

    border-radius:10px;

}

.students-table-wrapper::-webkit-scrollbar-track{

    background:#efefef;

}

/* Mobile */

@media(max-width:576px){

.student-avatar{

    width:30px;

    height:30px;

}

.card-custom h4{

    font-size:1.1rem;

}

.card-custom small{

    font-size:.80rem;

}

#studentsTable th,
#studentsTable td{

    font-size:.86rem;

    padding:.55rem;

}

}

/* Tablet */

@media(min-width:577px) and (max-width:991px){

#studentsTable th,
#studentsTable td{

    font-size:.92rem;

}

}

</style>

@endpush

@push('scripts')
<script>
$(function () {

    const table = $('#studentsTable').DataTable({

        processing: true,

        responsive: false,

        scrollX: true,

        autoWidth: false,

        deferRender: true,

        ajax: "{{ route('students.index') }}",

        columns: [

            {
                data: 'avatar_url',
                orderable: false,
                searchable: false,

                render: function (data, type, row) {

                    return `
                        <img
                            src="${data}"
                            class="student-avatar border"
                            alt="${row.name}">
                    `;

                }

            },

            {
                data: 'roll_number'
            },

            {
                data: 'name',

                render: function (data) {

                    return `<span class="fw-semibold">${data}</span>`;

                }

            },

            {
                data: 'email'
            },

            {
                data: 'class_name'
            },

            {
                data: 'section_name'
            },

            {
                data: 'gender'
            },

            {
                data: 'phone'
            },

            {
                data: 'actions',

                orderable: false,

                searchable: false,

                className: 'text-center'
            }

        ],

        pageLength: 10,

        order: [[1, 'asc']],

        language: {

            search: "_INPUT_",

            searchPlaceholder: "Search students...",

            lengthMenu: "Show _MENU_ students",

            info: "Showing _START_ to _END_ of _TOTAL_ students",

            zeroRecords: "No matching students found",

            emptyTable: "No students available"

        },
        initComplete: function () {

        this.api().columns.adjust();

    }

    });

    // Fix column widths when browser window is resized

    let resizeTimer;

    $(window).on('resize', function () {

        clearTimeout(resizeTimer);

        resizeTimer = setTimeout(function () {

            table.columns.adjust().draw(false);

        }, 150);

    });

    /*
    |--------------------------------------------------------------------------
    | Delete Student
    |--------------------------------------------------------------------------
    */

    $(document).on('click', '.delete-student-btn', function () {

        const studentId = $(this).data('id');

        if (confirm('Are you sure you want to delete this student profile?')) {

            $.ajax({

                url: "/students/" + studentId,

                type: "DELETE",

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function (response) {

                    alert(response.message);

                    table.ajax.reload(function () {

                    table.columns.adjust().draw(false);

                }, false);

                },

                error: function () {

                    alert('Error deleting student record.');

                }

            });

        }

    });

});
</script>
@endpush
