<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkAttendanceRequest;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display attendance bulk entry screen.
     */
    public function index(): View
    {
        $classes = SchoolClass::with('sections')->get();

        return view('attendance.index', compact('classes'));
    }

    /**
     * Fetch students and existing attendance status for AJAX requests.
     */
    public function fetchStudents(Request $request): JsonResponse
    {
        $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'date' => ['required', 'date'],
        ]);

        $students = Student::with('user')
            ->where('school_class_id', $request->school_class_id)
            ->where('section_id', $request->section_id)
            ->orderBy('roll_number')
            ->get();

        $attendances = Attendance::where('school_class_id', $request->school_class_id)
            ->where('section_id', $request->section_id)
            ->whereDate('date', $request->date)
            ->get()
            ->keyBy('student_id');

        $data = $students->map(function ($student) use ($attendances) {
            $existing = $attendances->get($student->id);

            return [
                'student_id' => $student->id,
                'roll_number' => $student->roll_number,
                'name' => $student->user->name,
                'status' => $existing ? $existing->status : 'present',
                'remarks' => $existing ? $existing->remarks : '',
            ];
        });

        return response()->json(['success' => true, 'students' => $data]);
    }

    /**
     * Store or update bulk attendance records.
     */
    public function store(BulkAttendanceRequest $request): JsonResponse
    {
        DB::transaction(function () use ($request) {
            foreach ($request->attendance as $item) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $item['student_id'],
                        'date' => $request->date,
                    ],
                    [
                        'school_class_id' => $request->school_class_id,
                        'section_id' => $request->section_id,
                        'status' => $item['status'],
                        'remarks' => $item['remarks'] ?? null,
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Attendance records saved successfully.',
        ]);
    }
}
