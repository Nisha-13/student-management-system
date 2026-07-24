<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Notifications\UserPortalAccessNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display a listing of students or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $students = Student::with(['user', 'schoolClass', 'section'])->latest()->get();

            return response()->json([
                'data' => $students->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'avatar_url' => $student->avatar_url,
                        'roll_number' => $student->roll_number,
                        'name' => $student->user->name,
                        'email' => $student->user->email,
                        'class_name' => $student->schoolClass->name ?? 'N/A',
                        'section_name' => $student->section->name ?? 'N/A',
                        'gender' => ucfirst($student->gender),
                        'phone' => $student->phone ?? 'N/A',
                        'actions' => view('students.partials.actions', compact('student'))->render(),
                    ];
                }),
            ]);
        }

        $classes = SchoolClass::with('sections')->get();

        return view('students.index', compact('classes'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create(): View
    {
        $classes = SchoolClass::with('sections')->get();

        return view('students.create', compact('classes'));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(StoreStudentRequest $request): RedirectResponse|JsonResponse
    {
        $user = null;
        DB::transaction(function () use ($request, &$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
            ]);

            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            Student::create([
                'user_id' => $user->id,
                'school_class_id' => $request->school_class_id,
                'section_id' => $request->section_id,
                'roll_number' => $request->roll_number,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'address' => $request->address,
                'avatar' => $avatarPath,
            ]);
        });

        $accessUrl = null;
        if ($user) {
            try {
                $user->notify(new UserPortalAccessNotification());
                session()->flash('email_sent', true);
            } catch (\Throwable $e) {
                \Log::error('Failed sending portal notification: ' . $e->getMessage());
                session()->flash('email_error', $e->getMessage());
            }

            $accessUrl = URL::temporarySignedRoute(
                'portal.access',
                now()->addHours(48),
                ['user' => $user->id]
            );

            session()->flash('access_url', $accessUrl);
            session()->flash('user_name', $user->name);
            session()->flash('user_email', $user->email);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Student created successfully. Portal access link generated.',
                'access_url' => $accessUrl
            ]);
        }

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student): View
    {
        $student->load(['user', 'schoolClass', 'section', 'attendances', 'marks.subject']);

        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student): View
    {
        $student->load(['user', 'schoolClass', 'section']);
        $classes = SchoolClass::with('sections')->get();

        return view('students.edit', compact('student', 'classes'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse|JsonResponse
    {
        DB::transaction(function () use ($request, $student) {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $student->user->update($userData);

            $avatarPath = $student->avatar;
            if ($request->hasFile('avatar')) {
                if ($student->avatar && Storage::disk('public')->exists($student->avatar)) {
                    Storage::disk('public')->delete($student->avatar);
                }
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            $student->update([
                'school_class_id' => $request->school_class_id,
                'section_id' => $request->section_id,
                'roll_number' => $request->roll_number,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'address' => $request->address,
                'avatar' => $avatarPath,
            ]);
        });

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Student updated successfully.']);
        }

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student, Request $request): RedirectResponse|JsonResponse
    {
        DB::transaction(function () use ($student) {
            if ($student->avatar && Storage::disk('public')->exists($student->avatar)) {
                Storage::disk('public')->delete($student->avatar);
            }
            $user = $student->user;
            $student->delete();
            $user?->delete();
        });

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Student deleted successfully.']);
        }

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }

    /**
     * Fetch sections for a given class via AJAX.
     */
    public function getSections(SchoolClass $schoolClass): JsonResponse
    {
        return response()->json($schoolClass->sections);
    }
}
