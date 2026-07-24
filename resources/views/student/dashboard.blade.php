@extends('layouts.app')

@section('title', 'Student Portal - Student Management System')
@section('page-title', 'My Academic Profile')

@section('content')

@if($student)
<div class="row g-4">
    {{-- Profile Card --}}
    <div class="col-12 col-md-4">
        <div class="card card-custom p-4 text-center h-100">
            <div class="mb-3">
                <img src="{{ $student->avatar_url }}" class="rounded-circle border shadow-sm"
                     width="90" height="90" style="object-fit:cover;" alt="{{ auth()->user()->name }}">
            </div>
            <h4 class="fw-bold m-0">{{ auth()->user()->name }}</h4>
            <p class="text-muted small mb-3">{{ auth()->user()->email }}</p>

            <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                <span class="badge bg-primary">Roll: {{ $student->roll_number }}</span>
                <span class="badge bg-secondary">{{ $student->schoolClass->name ?? 'N/A' }}</span>
                <span class="badge bg-info">{{ $student->section->name ?? 'N/A' }}</span>
                <span class="badge bg-dark text-capitalize">{{ $student->gender }}</span>
            </div>

            <a href="{{ route('students.report-card', $student->id) }}"
               class="btn btn-primary btn-sm rounded-pill w-100" target="_blank">
                <i class="bi bi-printer me-1"></i> Print My Report Card
            </a>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-12 col-md-8">

        {{-- Attendance Summary --}}
        <div class="card card-custom p-3 p-md-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-calendar-check text-warning me-2"></i> Attendance Record</h5>
            @php
                $totalAtt   = $student->attendances->count();
                $presentAtt = $student->attendances->where('status', 'present')->count();
                $absentAtt  = $student->attendances->where('status', 'absent')->count();
                $perc       = $totalAtt > 0 ? round(($presentAtt / $totalAtt) * 100, 1) : 100;
                $barColor   = $perc >= 75 ? 'bg-success' : ($perc >= 50 ? 'bg-warning' : 'bg-danger');
            @endphp
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small text-muted">Attendance Percentage</span>
                <span class="fw-bold {{ $perc >= 75 ? 'text-success' : 'text-danger' }}">{{ $perc }}%</span>
            </div>
            <div class="progress mb-3" style="height:12px;">
                <div class="progress-bar {{ $barColor }}" role="progressbar"
                     style="width:{{ $perc }}%" aria-valuenow="{{ $perc }}" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
            <div class="row g-2 text-center">
                <div class="col-4">
                    <div class="bg-light rounded p-2">
                        <div class="fw-bold fs-5 text-dark">{{ $totalAtt }}</div>
                        <small class="text-muted">Total Days</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-success-subtle rounded p-2">
                        <div class="fw-bold fs-5 text-success">{{ $presentAtt }}</div>
                        <small class="text-muted">Present</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-danger-subtle rounded p-2">
                        <div class="fw-bold fs-5 text-danger">{{ $absentAtt }}</div>
                        <small class="text-muted">Absent</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Examination Results --}}
        <div class="card card-custom p-3 p-md-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-journal-bookmark text-success me-2"></i> Examination Results</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Subject</th>
                            <th>Exam</th>
                            <th class="text-center">Score</th>
                            <th class="text-center d-none d-sm-table-cell">Max</th>
                            <th class="text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->marks as $mark)
                            @php $subPerc = $mark->max_marks > 0 ? round(($mark->marks_obtained / $mark->max_marks) * 100, 1) : 0; @endphp
                            <tr>
                                <td class="fw-semibold">{{ $mark->subject->name ?? 'N/A' }}</td>
                                <td>{{ $mark->exam_name }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $subPerc >= 50 ? 'bg-success' : 'bg-danger' }}">{{ $mark->marks_obtained }}</span>
                                </td>
                                <td class="text-center d-none d-sm-table-cell">{{ $mark->max_marks }}</td>
                                <td class="text-center">{{ $subPerc }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No exam marks recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Fee Status --}}
        <div class="card card-custom p-3 p-md-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-cash-stack text-info me-2"></i> My Fee Status</h5>
            @php
                $totalFee = $student->fees->sum('amount');
                $paidFee  = $student->fees->sum('paid_amount');
                $dueFee   = max(0, $totalFee - $paidFee);
            @endphp
            <div class="row g-3 text-center">
                <div class="col-4">
                    <div class="bg-light rounded p-2 p-sm-3">
                        <div class="fw-bold fs-6 fs-sm-5">${{ number_format($totalFee, 2) }}</div>
                        <small class="text-muted">Total Billed</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-success-subtle rounded p-2 p-sm-3">
                        <div class="fw-bold fs-6 fs-sm-5 text-success">${{ number_format($paidFee, 2) }}</div>
                        <small class="text-muted">Amount Paid</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-danger-subtle rounded p-2 p-sm-3">
                        <div class="fw-bold fs-6 fs-sm-5 text-danger">${{ number_format($dueFee, 2) }}</div>
                        <small class="text-muted">Outstanding</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@else
<div class="alert alert-warning d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
    <div>Your student profile has not been set up yet. Please contact the school administration.</div>
</div>
@endif

@endsection
