@extends('layouts.app')

@section('title', 'Student Profile - Student Management System')
@section('page-title', 'Student Profile')

@section('content')
<div class="row g-4">
    {{-- Profile Card --}}
    <div class="col-12 col-md-4">
        <div class="card card-custom p-4 text-center h-100">
            <div class="mb-3">
                <img src="{{ $student->avatar_url }}" class="rounded-circle border shadow"
                     width="100" height="100" style="object-fit:cover;" alt="{{ $student->user->name }}">
            </div>
            <h4 class="fw-bold m-0">{{ $student->user->name }}</h4>
            <p class="text-muted small mb-3">{{ $student->user->email }}</p>

            <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                <span class="badge bg-primary">Roll: {{ $student->roll_number }}</span>
                <span class="badge bg-secondary">{{ $student->schoolClass->name ?? 'N/A' }}</span>
                <span class="badge bg-info">{{ $student->section->name ?? 'N/A' }}</span>
            </div>

            <div class="d-flex flex-column gap-2">
                <a href="{{ route('students.report-card', $student->id) }}" class="btn btn-primary btn-sm rounded-pill" target="_blank">
                    <i class="bi bi-printer me-1"></i> Print Report Card
                </a>
                <a href="{{ route('students.edit', $student->id) }}" class="btn btn-outline-warning btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Edit Profile
                </a>
                {{-- Send Portal Access Link --}}
                <form action="{{ route('users.resend-portal-link', $student->user_id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill w-100">
                        <i class="bi bi-envelope me-1"></i> Send Access Link
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Details Column --}}
    <div class="col-12 col-md-8">
        {{-- Personal & Contact Info --}}
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3">Personal &amp; Contact Info</h5>
            <div class="row g-3">
                <div class="col-6 col-sm-6">
                    <span class="text-muted small d-block">Gender</span>
                    <span class="fw-semibold text-capitalize">{{ $student->gender }}</span>
                </div>
                <div class="col-6 col-sm-6">
                    <span class="text-muted small d-block">Date of Birth</span>
                    <span class="fw-semibold">{{ optional($student->dob)->format('M d, Y') ?? 'N/A' }}</span>
                </div>
                <div class="col-6 col-sm-6">
                    <span class="text-muted small d-block">Phone Number</span>
                    <span class="fw-semibold">{{ $student->phone ?? 'N/A' }}</span>
                </div>
                <div class="col-12 col-sm-6">
                    <span class="text-muted small d-block">Address</span>
                    <span class="fw-semibold">{{ $student->address ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        {{-- Academic Performance --}}
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3">Academic Performance Summary</h5>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Subject</th>
                            <th>Exam</th>
                            <th>Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->marks as $mark)
                            <tr>
                                <td>{{ $mark->subject->name ?? 'N/A' }}</td>
                                <td>{{ $mark->exam_name }}</td>
                                <td><span class="badge bg-success">{{ $mark->marks_obtained }} / {{ $mark->max_marks }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No examination marks logged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
