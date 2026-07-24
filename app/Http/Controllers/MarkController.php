<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkMarkRequest;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MarkController extends Controller
{
    /**
     * Display bulk marks management page.
     */
    public function index(): View
    {
        $classes = SchoolClass::with('subjects')->get();

        return view('marks.index', compact('classes'));
    }

    /**
     * Fetch subjects for a given class via AJAX.
     */
    public function getSubjects(SchoolClass $schoolClass): JsonResponse
    {
        return response()->json($schoolClass->subjects);
    }

    /**
     * Fetch students and existing marks for AJAX.
     */
    public function fetchStudents(Request $request): JsonResponse
    {
        $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'exam_name' => ['required', 'string'],
        ]);

        $students = Student::with('user')
            ->where('school_class_id', $request->school_class_id)
            ->orderBy('roll_number')
            ->get();

        $existingMarks = Mark::where('school_class_id', $request->school_class_id)
            ->where('subject_id', $request->subject_id)
            ->where('exam_name', $request->exam_name)
            ->get()
            ->keyBy('student_id');

        $data = $students->map(function ($student) use ($existingMarks) {
            $existing = $existingMarks->get($student->id);

            return [
                'student_id' => $student->id,
                'roll_number' => $student->roll_number,
                'name' => $student->user->name,
                'marks_obtained' => $existing ? $existing->marks_obtained : '',
                'max_marks' => $existing ? $existing->max_marks : 100,
                'remarks' => $existing ? $existing->remarks : '',
            ];
        });

        return response()->json(['success' => true, 'students' => $data]);
    }

    /**
     * Store or update bulk marks records.
     */
    public function store(BulkMarkRequest $request): JsonResponse
    {
        DB::transaction(function () use ($request) {
            foreach ($request->marks as $item) {
                Mark::updateOrCreate(
                    [
                        'student_id' => $item['student_id'],
                        'subject_id' => $request->subject_id,
                        'exam_name' => $request->exam_name,
                    ],
                    [
                        'school_class_id' => $request->school_class_id,
                        'marks_obtained' => $item['marks_obtained'],
                        'max_marks' => $item['max_marks'] ?? 100,
                        'remarks' => $item['remarks'] ?? null,
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Marks records saved successfully.',
        ]);
    }
}
