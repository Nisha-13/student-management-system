@extends('layouts.app')

@section('title', 'Add Teacher - Student Management System')
@section('page-title', 'Create Teacher Profile')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0">Add New Faculty Member</h5>
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

    <form action="{{ route('admin.teachers.store') }}" method="POST">
        @csrf
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-muted">Full Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-muted">Email Address *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-muted">Password *</label>
                <input type="password" name="password" class="form-control" required minlength="8">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-muted">Employee ID *</label>
                <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id') }}" placeholder="e.g. TCH-002" required>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-muted">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-muted">Qualification</label>
                <input type="text" name="qualification" class="form-control" value="{{ old('qualification') }}" placeholder="e.g. M.Sc. Physics">
            </div>
        </div>

        <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Teacher Profile</button>
    </form>
</div>
@endsection
