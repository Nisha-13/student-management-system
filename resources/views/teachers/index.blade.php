@extends('layouts.app')

@section('title', 'Teachers List - Student Management System')
@section('page-title', 'Teacher Management')

@section('content')

<div class="card card-custom shadow-sm border-0">

    <div class="card-body p-3 p-md-4">

        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

            <div>

                <h4 class="fw-bold mb-1">
                    Faculty Directory
                </h4>

                <small class="text-muted">
                    Manage all teachers and academic staff members
                </small>

            </div>

            <a href="{{ route('admin.teachers.create') }}"
               class="btn btn-primary rounded-pill px-4 add-teacher-btn">

                <i class="bi bi-plus-lg me-1"></i>

                Add New Teacher

            </a>

        </div>

        <!-- Table -->
        <div class="table-responsive teachers-table-wrapper">

            <table
                id="teachersTable"
                class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>

                        <th>Employee ID</th>

                        <th>Name</th>

                        <th>Email</th>

                        <th>Qualification</th>

                        <th>Phone</th>

                        <th class="text-center">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <!-- AJAX -->

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

.add-teacher-btn{

    white-space:nowrap;

}

@media(max-width:767px){

.add-teacher-btn{

    width:100%;

}

}

/* Table */

.teachers-table-wrapper{

    overflow-x:auto;

    overflow-y:hidden;

    -webkit-overflow-scrolling:touch;

}

#teachersTable{

    min-width:1000px;

    table-layout:auto;

}

#teachersTable thead th{

    font-weight:600;

    white-space:nowrap;

}

#teachersTable td{

    white-space:nowrap;

    vertical-align:middle;

}

#teachersTable th:last-child,
#teachersTable td:last-child{

    white-space:nowrap;

    text-align:center;

    min-width:130px;

}

/* Scrollbar */

.teachers-table-wrapper::-webkit-scrollbar{

    height:8px;

}

.teachers-table-wrapper::-webkit-scrollbar-thumb{

    background:#c8c8c8;

    border-radius:10px;

}

.teachers-table-wrapper::-webkit-scrollbar-track{

    background:#efefef;

}

/* Mobile */

@media(max-width:576px){

.card-custom h4{

    font-size:1.1rem;

}

.card-custom small{

    font-size:.80rem;

}

#teachersTable th,
#teachersTable td{

    font-size:.86rem;

    padding:.55rem;

}

}

/* Tablet */

@media(min-width:577px) and (max-width:991px){

#teachersTable th,
#teachersTable td{

    font-size:.92rem;

}

}

</style>

@endpush


@push('scripts')

<script>

$(function(){

    const table = $('#teachersTable').DataTable({

        processing:true,

        responsive:false,

        scrollX:true,

        autoWidth:false,

        deferRender: true,

        ajax:"{{ route('admin.teachers.index') }}",

        columns:[

            {
                data:'employee_id'
            },

            {
                data:'name',

                render:function(data){

                    return `<span class="fw-semibold">${data}</span>`;

                }

            },

            {
                data:'email'
            },

            {
                data:'qualification'
            },

            {
                data:'phone'
            },

            {
                data:'actions',

                orderable:false,

                searchable:false,

                className:'text-center'
            }

        ],

        pageLength:10,

        order:[[0,'asc']],

        language: {

        search: "_INPUT_",

        searchPlaceholder: "Search teachers...",

        lengthMenu: "Show _MENU_ teachers",

        info: "Showing _START_ to _END_ of _TOTAL_ teachers",

        zeroRecords: "No matching teachers found",

        emptyTable: "No teachers available"

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
    | Delete Teacher
    |--------------------------------------------------------------------------
    */

    $(document).on('click','.delete-teacher-btn',function(){

        const teacherId=$(this).data('id');

        if(confirm('Are you sure you want to delete this teacher record?')){

            $.ajax({

                url:"/admin/teachers/"+teacherId,

                type:"DELETE",

                headers:{
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },

                success: function (response) {

                    alert(response.message);

                    table.ajax.reload(function () {

                    table.columns.adjust().draw(false);

                }, false);

                },
                error:function(){

                    alert('Error deleting teacher record.');

                }

            });

        }

    });

});

</script>

@endpush
