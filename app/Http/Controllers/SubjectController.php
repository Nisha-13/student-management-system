<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $subjects = Subject::with('schoolClass')->latest()->get();

            return response()->json([
                'data' => $subjects->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'name' => $s->name,
                        'code' => $s->code,
                        'class_name' => $s->schoolClass->name ?? '—',
                        'actions' => '
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-sm btn-outline-warning edit-subject-btn"
                                    data-id="'.$s->id.'" data-name="'.e($s->name).'" data-code="'.e($s->code).'" data-class="'.$s->school_class_id.'"
                                    title="Edit Subject">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-subject-btn" data-id="'.$s->id.'" title="Delete Subject">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        ',
                    ];
                }),
            ]);
        }

        $classes = SchoolClass::all();

        return view('subjects.index', compact('classes'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20', 'unique:subjects,code'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
        ]);

        Subject::create($validated);

        return response()->json(['success' => true, 'message' => 'Subject created successfully.']);
    }

    public function update(Request $request, Subject $subject): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20', 'unique:subjects,code,' . $subject->id],
            'school_class_id' => ['required', 'exists:school_classes,id'],
        ]);

        $subject->update($validated);

        return response()->json(['success' => true, 'message' => 'Subject updated successfully.']);
    }

    public function destroy(Subject $subject): JsonResponse
    {
        if ($subject->marks()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete — marks exist for this subject.'], 422);
        }

        $subject->delete();

        return response()->json(['success' => true, 'message' => 'Subject deleted successfully.']);
    }
}
