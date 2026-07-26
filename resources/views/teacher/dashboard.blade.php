@extends('layouts.app')

@section('title', 'Teacher Dashboard - Student Management System')
@section('page-title', 'Teacher Portal')

@section('content')

<div class="row g-4 mb-4">

    <!-- Students Directory -->
    <div class="col-12 col-md-4">
        <div class="card card-custom p-4 text-center">

            <i class="bi bi-people-fill text-primary display-4 mb-2"></i>

            <h5 class="fw-bold">
                Students Directory
            </h5>

            <p class="text-muted small">
                View student profiles, class rosters, and contact info.
            </p>

            <a href="{{ route('students.index') }}"
               class="btn btn-primary btn-sm rounded-pill">

                Manage Students

            </a>

        </div>
    </div>



    <!-- Attendance -->
    <div class="col-12 col-md-4">
        <div class="card card-custom p-4 text-center">

            <i class="bi bi-calendar-check-fill text-warning display-4 mb-2"></i>

            <h5 class="fw-bold">
                Attendance Register
            </h5>

            <p class="text-muted small">
                Take daily bulk attendance for your designated section.
            </p>

            <a href="{{ route('attendance.index') }}"
               class="btn btn-warning btn-sm rounded-pill text-dark">

                Take Attendance

            </a>

        </div>
    </div>




    <!-- Marks -->
    <div class="col-12 col-md-4">
        <div class="card card-custom p-4 text-center">

            <i class="bi bi-journal-plus text-success display-4 mb-2"></i>

            <h5 class="fw-bold">
                Subject Marks
            </h5>

            <p class="text-muted small">
                Record and update mid-term and final examination scores.
            </p>

            <a href="{{ route('marks.index') }}"
               class="btn btn-success btn-sm rounded-pill">

                Enter Marks

            </a>

        </div>
    </div>




    <!-- Fee Management -->
    <div class="col-12 col-md-4">
        <div class="card card-custom p-4 text-center">

            <i class="bi bi-cash-stack text-danger display-4 mb-2"></i>

            <h5 class="fw-bold">
                Fee Management
            </h5>

            <p class="text-muted small">
                View student fee records, payments, and outstanding dues.
            </p>

            <a href="{{ route('fees.index') }}"
               class="btn btn-danger btn-sm rounded-pill">

                Manage Fees

            </a>

        </div>
    </div>




    <!-- Timetable -->
    <div class="col-12 col-md-4">
        <div class="card card-custom p-4 text-center">

            <i class="bi bi-calendar3 text-info display-4 mb-2"></i>

            <h5 class="fw-bold">
                Class Timetable
            </h5>

            <p class="text-muted small">
                View class schedules, subjects, and assigned teaching periods.
            </p>

            <a href="{{ route('timetables.index') }}"
               class="btn btn-info btn-sm rounded-pill text-white">

                View Timetable

            </a>

        </div>
    </div>


</div>

@endsection
