<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkMarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'exam_name' => ['required', 'string', 'max:100'],
            'marks' => ['required', 'array', 'min:1'],
            'marks.*.student_id' => ['required', 'exists:students,id'],
            'marks.*.marks_obtained' => ['required', 'numeric', 'min:0', 'lte:marks.*.max_marks'],
            'marks.*.max_marks' => ['required', 'numeric', 'min:1'],
            'marks.*.remarks' => ['nullable', 'string', 'max:255'],
        ];
    }
}
