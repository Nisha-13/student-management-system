<div class="d-flex gap-1 justify-content-center">
    <form action="{{ route('users.resend-portal-link', $teacher->user_id) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-primary" title="Send/Resend Access Link">
            <i class="bi bi-envelope"></i>
        </button>
    </form>
    <a href="{{ route('admin.teachers.show', $teacher->id) }}" class="btn btn-sm btn-outline-info" title="View Teacher">
        <i class="bi bi-eye"></i>
    </a>
    <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn btn-sm btn-outline-warning" title="Edit Teacher">
        <i class="bi bi-pencil"></i>
    </a>
    <button type="button" class="btn btn-sm btn-outline-danger delete-teacher-btn" data-id="{{ $teacher->id }}" title="Delete Teacher">
        <i class="bi bi-trash"></i>
    </button>
</div>
