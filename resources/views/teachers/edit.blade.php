@extends('layouts.app')

@section('title', 'Edit Teacher - Student Management System')
@section('page-title', 'Edit Teacher Profile')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0">Edit Faculty Profile</h5>
        <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back to List</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.teachers.update', $teacher->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-muted">Full Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $teacher->user->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-muted">Email Address *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $teacher->user->email) }}" required>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-muted">Password (Leave blank to keep unchanged)</label>
                <input type="password" name="password" class="form-control" minlength="8">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-muted">Employee ID *</label>
                <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id', $teacher->employee_id) }}" required>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-muted">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $teacher->phone) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-muted">Qualification</label>
                <input type="text" name="qualification" class="form-control" value="{{ old('qualification', $teacher->qualification) }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary px-4 fw-semibold">Update Teacher Profile</button>
    </form>
</div>
@endsection
