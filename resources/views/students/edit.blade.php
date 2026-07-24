@extends('layouts.app')

@section('title', 'Edit Student - Student Management System')
@section('page-title', 'Edit Student Profile')

@section('content')
<div class="card card-custom p-3 p-md-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h5 class="fw-bold m-0">Edit Student Record</h5>
            <small class="text-muted">Update student information and save changes</small>
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

    <form action="{{ route('students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Current Avatar + Upload --}}
        <div class="d-flex flex-wrap align-items-center gap-3 mb-4 p-3 bg-light rounded-3">
            <img src="{{ $student->avatar_url }}" class="rounded-circle border shadow-sm flex-shrink-0"
                 width="64" height="64" style="object-fit:cover;" alt="Current Photo">
            <div class="flex-grow-1">
                <label class="form-label fw-semibold small text-muted mb-1">Change Profile Photo</label>
                <input type="file" name="avatar" class="form-control form-control-sm" accept="image/*">
                <small class="text-muted">Upload a new photo to replace the current one (Max 2MB)</small>
            </div>
        </div>

        {{-- Name & Email --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small text-muted">Full Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $student->user->name) }}" required>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small text-muted">Email Address *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $student->user->email) }}" required>
            </div>
        </div>

        {{-- Password & Roll Number --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small text-muted">Password <span class="text-muted">(Leave blank to keep unchanged)</span></label>
                <input type="password" name="password" class="form-control" minlength="8" placeholder="Leave blank to keep existing">
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small text-muted">Roll Number *</label>
                <input type="text" name="roll_number" class="form-control" value="{{ old('roll_number', $student->roll_number) }}" required>
            </div>
        </div>

        {{-- Class & Section --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small text-muted">Class *</label>
                <select name="school_class_id" id="classSelect" class="form-select" required>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('school_class_id', $student->school_class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small text-muted">Section *</label>
                <select name="section_id" id="sectionSelect" class="form-select" required>
                    @foreach($student->schoolClass->sections as $sec)
                        <option value="{{ $sec->id }}" {{ old('section_id', $student->section_id) == $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- DOB, Gender, Phone --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small text-muted">Date of Birth</label>
                <input type="date" name="dob" class="form-control" value="{{ old('dob', optional($student->dob)->format('Y-m-d')) }}">
            </div>
            <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small text-muted">Gender *</label>
                <select name="gender" class="form-select" required>
                    <option value="male"   {{ old('gender', $student->gender) == 'male'   ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other"  {{ old('gender', $student->gender) == 'other'  ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small text-muted">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->phone) }}">
            </div>
        </div>

        {{-- Address --}}
        <div class="mb-4">
            <label class="form-label fw-semibold small text-muted">Residential Address</label>
            <textarea name="address" class="form-control" rows="2">{{ old('address', $student->address) }}</textarea>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="bi bi-save me-1"></i> Update Student Profile
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
                    var options = '';
                    $.each(sections, function(index, sec) {
                        options += '<option value="' + sec.id + '">' + sec.name + '</option>';
                    });
                    sectionSelect.html(options);
                }
            });
        }
    });
</script>
@endpush
