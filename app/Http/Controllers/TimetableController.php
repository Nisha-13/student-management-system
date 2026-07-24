<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Timetable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimetableController extends Controller
{
    /**
     * Display timetable view.
     */
    public function index(): View
    {
        $classes = SchoolClass::with(['sections', 'subjects'])->get();
        $teachers = Teacher::with('user')->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        return view('timetables.index', compact('classes', 'teachers', 'days'));
    }

    /**
     * Fetch timetable grid for a given class & section.
     */
    public function fetchGrid(Request $request): JsonResponse
    {
        $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
        ]);

        $slots = Timetable::with(['subject', 'teacher.user'])
            ->where('school_class_id', $request->school_class_id)
            ->where('section_id', $request->section_id)
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'slots' => $slots,
        ]);
    }

    /**
     * Store new timetable period.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'day_of_week' => ['required', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room_number' => ['nullable', 'string', 'max:50'],
        ]);

        Timetable::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Class timetable period scheduled successfully.',
            'school_class_id' => $validated['school_class_id'],
            'section_id' => $validated['section_id'],
        ]);
    }

    /**
     * Delete a period slot.
     */
    public function destroy(Timetable $timetable): JsonResponse
    {
        $timetable->delete();

        return response()->json(['success' => true, 'message' => 'Timetable slot removed successfully.']);
    }
}
