@extends('layouts.app')

@section('title', 'Teacher Profile - Student Management System')
@section('page-title', 'Faculty Member Profile')

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="card card-custom p-4 text-center">
            <i class="bi bi-person-badge-fill text-success display-2 mb-2"></i>
            <h4 class="fw-bold m-0">{{ $teacher->user->name }}</h4>
            <p class="text-muted small mb-3">{{ $teacher->user->email }}</p>
            <span class="badge bg-success mb-3">Employee ID: {{ $teacher->employee_id }}</span>
            <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn btn-outline-warning btn-sm rounded-pill w-100">
                <i class="bi bi-pencil me-1"></i> Edit Profile
            </a>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3">Faculty Details</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <span class="text-muted small d-block">Qualification</span>
                    <span class="fw-semibold">{{ $teacher->qualification ?? 'N/A' }}</span>
                </div>
                <div class="col-md-6">
                    <span class="text-muted small d-block">Phone Number</span>
                    <span class="fw-semibold">{{ $teacher->phone ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
