@extends('layouts.app')

@section('title', 'Add Student - Student Management System')
@section('page-title', 'Create Student Profile')

@section('content')
<div class="card card-custom p-3 p-md-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h5 class="fw-bold m-0">Add New Student</h5>
            <small class="text-muted">Fill in all required fields to register a new student</small>
        </div>
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
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

    <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Avatar Upload --}}
        <div class="mb-3">
            <label class="form-label fw-semibold small text-muted">Profile Photo / Avatar</label>
            <input type="file" name="avatar" class="form-control" accept="image/*">
            <small class="text-muted">Supported: JPG, PNG, WEBP (Max 2MB)</small>
        </div>

        {{-- Name & Email --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small text-muted">Full Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Student full name">
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small text-muted">Email Address *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="student@email.com">
                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Portal access link will be emailed here</small>
            </div>
        </div>

        {{-- Password & Roll Number --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small text-muted">Password *</label>
                <input type="password" name="password" class="form-control" required minlength="8" placeholder="Min. 8 characters">
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small text-muted">Roll Number *</label>
                <input type="text" name="roll_number" class="form-control" value="{{ old('roll_number') }}" required placeholder="e.g. 2024-001">
            </div>
        </div>

        {{-- Class & Section --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small text-muted">Class *</label>
                <select name="school_class_id" id="classSelect" class="form-select" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('school_class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small text-muted">Section *</label>
                <select name="section_id" id="sectionSelect" class="form-select" required>
                    <option value="">Select Section</option>
                </select>
            </div>
        </div>

        {{-- DOB, Gender, Phone --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small text-muted">Date of Birth</label>
                <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
            </div>
            <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small text-muted">Gender *</label>
                <select name="gender" class="form-select" required>
                    <option value="male"   {{ old('gender') == 'male'   ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other"  {{ old('gender') == 'other'  ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small text-muted">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+92 300 0000000">
            </div>
        </div>

        {{-- Address --}}
        <div class="mb-4">
            <label class="form-label fw-semibold small text-muted">Residential Address</label>
            <textarea name="address" class="form-control" rows="2" placeholder="Street, City, Country">{{ old('address') }}</textarea>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="bi bi-person-plus me-1"></i> Save Student
            </button>
            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $('#classSelect').on('change', function() {
        var classId = $(this).val();
        var sectionSelect = $('#sectionSelect');
        sectionSelect.html('<option value="">Loading sections...</option>');

        if (classId) {
            $.ajax({
                url: "/classes/" + classId + "/sections",
                type: 'GET',
                success: function(sections) {
                    var options = '<option value="">Select Section</option>';
                    $.each(sections, function(index, sec) {
                        options += '<option value="' + sec.id + '">' + sec.name + '</option>';
                    });
                    sectionSelect.html(options);
                }
            });
        } else {
            sectionSelect.html('<option value="">Select Section</option>');
        }
    });
</script>
@endpush
