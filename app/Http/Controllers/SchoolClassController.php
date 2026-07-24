<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $classes = SchoolClass::withCount(['sections', 'students', 'subjects'])->latest()->get();

            return response()->json([
                'data' => $classes->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'name' => $c->name,
                        'code' => $c->code ?? '—',
                        'sections_count' => $c->sections_count,
                        'students_count' => $c->students_count,
                        'subjects_count' => $c->subjects_count,
                        'actions' => '
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-sm btn-outline-warning edit-class-btn"
                                    data-id="'.$c->id.'" data-name="'.e($c->name).'" data-code="'.e($c->code).'"
                                    title="Edit Class">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-class-btn" data-id="'.$c->id.'" title="Delete Class">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        ',
                    ];
                }),
            ]);
        }

        return view('classes.index');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:school_classes,name'],
            'code' => ['nullable', 'string', 'max:20', 'unique:school_classes,code'],
        ]);

        SchoolClass::create($validated);

        return response()->json(['success' => true, 'message' => 'Class created successfully.']);
    }

    public function update(Request $request, SchoolClass $schoolClass): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:school_classes,name,' . $schoolClass->id],
            'code' => ['nullable', 'string', 'max:20', 'unique:school_classes,code,' . $schoolClass->id],
        ]);

        $schoolClass->update($validated);

        return response()->json(['success' => true, 'message' => 'Class updated successfully.']);
    }

    public function destroy(SchoolClass $schoolClass): JsonResponse
    {
        if ($schoolClass->students()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete — students are enrolled in this class.'], 422);
        }

        $schoolClass->delete();

        return response()->json(['success' => true, 'message' => 'Class deleted successfully.']);
    }

    // --- Sections sub-resource ---
    public function storeSections(Request $request, SchoolClass $schoolClass): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ]);

        $schoolClass->sections()->create([
            'name' => $request->name,
            'capacity' => $request->capacity ?? 40,
        ]);

        return response()->json(['success' => true, 'message' => 'Section added successfully.']);
    }

    public function destroySection(Section $section): JsonResponse
    {
        if ($section->students()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete — students are in this section.'], 422);
        }

        $section->delete();

        return response()->json(['success' => true, 'message' => 'Section deleted successfully.']);
    }

    public function sections(SchoolClass $schoolClass): JsonResponse
    {
        return response()->json($schoolClass->sections()->withCount('students')->get());
    }
}
