@extends('layouts.app')

@section('title', 'Admin Dashboard - Student Management System')
@section('page-title', 'Admin Overview')

@section('content')

{{-- Stats Row --}}
<div class="row g-3 g-md-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 h-100 border-start border-primary border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size:0.7rem;">Total Students</span>
                    <h3 class="fw-bold m-0 mt-1 text-dark">{{ $stats['students'] }}</h3>
                </div>
                <div class="bg-primary-subtle text-primary p-2 p-md-3 rounded-circle fs-4">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 h-100 border-start border-success border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size:0.7rem;">Total Teachers</span>
                    <h3 class="fw-bold m-0 mt-1 text-dark">{{ $stats['teachers'] }}</h3>
                </div>
                <div class="bg-success-subtle text-success p-2 p-md-3 rounded-circle fs-4">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 h-100 border-start border-warning border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size:0.7rem;">Classes</span>
                    <h3 class="fw-bold m-0 mt-1 text-dark">{{ $stats['classes'] }}</h3>
                </div>
                <div class="bg-warning-subtle text-warning p-2 p-md-3 rounded-circle fs-4">
                    <i class="bi bi-building"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 h-100 border-start border-info border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size:0.7rem;">Subjects</span>
                    <h3 class="fw-bold m-0 mt-1 text-dark">{{ $stats['subjects'] }}</h3>
                </div>
                <div class="bg-info-subtle text-info p-2 p-md-3 rounded-circle fs-4">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Finance & Activity Row --}}
<div class="row g-3 g-md-4 mb-4">
    <div class="col-12 col-sm-4">
        <div class="card card-custom p-3 h-100 border-start border-success border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size:0.7rem;">Fees Collected</span>
                    <h3 class="fw-bold m-0 mt-1 text-success">${{ number_format($stats['collectedFees'], 2) }}</h3>
                    <small class="text-muted">of ${{ number_format($stats['totalFees'], 2) }} total</small>
                </div>
                <div class="bg-success-subtle text-success p-2 p-md-3 rounded-circle fs-4">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-4">
        <div class="card card-custom p-3 h-100 border-start border-danger border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size:0.7rem;">Unpaid Fee Records</span>
                    <h3 class="fw-bold m-0 mt-1 text-danger">{{ $stats['unpaidFees'] }}</h3>
                    <small class="text-muted">Pending demands</small>
                </div>
                <div class="bg-danger-subtle text-danger p-2 p-md-3 rounded-circle fs-4">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-4">
        <div class="card card-custom p-3 h-100 border-start border-info border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size:0.7rem;">Today's Attendance</span>
                    <h3 class="fw-bold m-0 mt-1 text-info">{{ $stats['todayAttendance'] }}</h3>
                    <small class="text-muted">{{ now()->format('d M Y') }}</small>
                </div>
                <div class="bg-info-subtle text-info p-2 p-md-3 rounded-circle fs-4">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="card card-custom p-3 p-md-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-lightning-charge text-warning me-2"></i> Quick Actions</h5>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('students.index') }}" class="btn btn-outline-primary btn-sm rounded-pill">
            <i class="bi bi-people me-1"></i> Manage Students
        </a>
        <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-success btn-sm rounded-pill">
            <i class="bi bi-person-badge me-1"></i> Manage Teachers
        </a>
        <a href="{{ route('attendance.index') }}" class="btn btn-outline-warning btn-sm rounded-pill">
            <i class="bi bi-calendar-check me-1"></i> Daily Attendance
        </a>
        <a href="{{ route('marks.index') }}" class="btn btn-outline-info btn-sm rounded-pill">
            <i class="bi bi-journal-plus me-1"></i> Marks Entry
        </a>
        <a href="{{ route('fees.index') }}" class="btn btn-outline-success btn-sm rounded-pill">
            <i class="bi bi-cash me-1"></i> Fee Collection
        </a>
        <a href="{{ route('timetables.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="bi bi-calendar3 me-1"></i> Class Timetable
        </a>
        <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-warning btn-sm rounded-pill">
            <i class="bi bi-building me-1"></i> Classes &amp; Sections
        </a>
        <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-info btn-sm rounded-pill">
            <i class="bi bi-book me-1"></i> Subjects
        </a>
        <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm rounded-pill">
            <i class="bi bi-plus-circle me-1"></i> Add New Student
        </a>
    </div>
</div>
@endsection
