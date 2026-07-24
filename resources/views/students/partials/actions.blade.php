<div class="d-flex gap-1 justify-content-center dt-action-btns flex-nowrap">
    {{-- Send / Resend Portal Access Link --}}
    <form action="{{ route('users.resend-portal-link', $student->user_id) }}" method="POST" class="d-inline"
          title="Send or Resend a secure one-click portal login link to this student's email inbox"
          data-bs-toggle="tooltip" data-bs-placement="top">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-primary" title="Send/Resend Access Link">
            <i class="bi bi-envelope"></i>
        </button>
    </form>

    <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-outline-info"
       title="View Profile" data-bs-toggle="tooltip" data-bs-placement="top">
        <i class="bi bi-eye"></i>
    </a>

    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-outline-warning"
       title="Edit Student" data-bs-toggle="tooltip" data-bs-placement="top">
        <i class="bi bi-pencil"></i>
    </a>

    <button type="button" class="btn btn-sm btn-outline-danger delete-student-btn"
            data-id="{{ $student->id }}" title="Delete Student"
            data-bs-toggle="tooltip" data-bs-placement="top">
        <i class="bi bi-trash"></i>
    </button>
</div>

<script>
    // Initialize tooltips inside DataTable rendered HTML
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el, { trigger: 'hover' });
    });
</script>
