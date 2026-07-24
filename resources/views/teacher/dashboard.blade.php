@extends('layouts.app')

@section('title', 'Teacher Dashboard - Student Management System')
@section('page-title', 'Teacher Portal')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card card-custom p-4 text-center">
            <i class="bi bi-people-fill text-primary display-4 mb-2"></i>
            <h5 class="fw-bold">Students Directory</h5>
            <p class="text-muted small">View student profiles, class rosters, and contact info.</p>
            <a href="{{ route('students.index') }}" class="btn btn-primary btn-sm rounded-pill">Manage Students</a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-custom p-4 text-center">
            <i class="bi bi-calendar-check-fill text-warning display-4 mb-2"></i>
            <h5 class="fw-bold">Attendance Register</h5>
            <p class="text-muted small">Take daily bulk attendance for your designated section.</p>
            <a href="{{ route('attendance.index') }}" class="btn btn-warning btn-sm rounded-pill text-dark">Take Attendance</a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-custom p-4 text-center">
            <i class="bi bi-journal-plus text-success display-4 mb-2"></i>
            <h5 class="fw-bold">Subject Marks</h5>
            <p class="text-muted small">Record and update mid-term and final examination scores.</p>
            <a href="{{ route('marks.index') }}" class="btn btn-success btn-sm rounded-pill">Enter Marks</a>
        </div>
    </div>
</div>
@endsection
