<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\UserPortalAccessNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $teachers = Teacher::with('user')->latest()->get();

            return response()->json([
                'data' => $teachers->map(function ($teacher) {
                    return [
                        'id' => $teacher->id,
                        'employee_id' => $teacher->employee_id,
                        'name' => $teacher->user->name,
                        'email' => $teacher->user->email,
                        'qualification' => $teacher->qualification ?? 'N/A',
                        'phone' => $teacher->phone ?? 'N/A',
                        'actions' => view('teachers.partials.actions', compact('teacher'))->render(),
                    ];
                }),
            ]);
        }

        return view('teachers.index');
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create(): View
    {
        return view('teachers.create');
    }

    /**
     * Store a newly created teacher in storage.
     */
    public function store(StoreTeacherRequest $request): RedirectResponse|JsonResponse
    {
        $user = null;
        DB::transaction(function () use ($request, &$user) {
            // Handle avatar upload if present
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            // Create user WITHOUT email verification (will be verified via email)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'teacher',
                'avatar' => $avatarPath,
                // email_verified_at is NULL by default (not verified)
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'employee_id' => $request->employee_id,
                'phone' => $request->phone,
                'qualification' => $request->qualification,
            ]);
        });

        $accessUrl = null;
        if ($user) {
            try {
                // Send both portal access link AND email verification
                $user->notify(new UserPortalAccessNotification());
                $user->sendEmailVerificationNotification(); // Send verification email
                session()->flash('email_sent', true);
            } catch (\Throwable $e) {
                \Log::error('Failed sending teacher notifications: ' . $e->getMessage());
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
                'message' => 'Teacher created successfully. Verification email sent.',
                'access_url' => $accessUrl
            ]);
        }

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher created successfully. Verification email sent.');
    }

    /**
     * Display the specified teacher.
     */
    public function show(Teacher $teacher): View
    {
        $teacher->load('user');

        return view('teachers.show', compact('teacher'));
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(Teacher $teacher): View
    {
        $teacher->load('user');

        return view('teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified teacher in storage.
     */
    public function update(UpdateTeacherRequest $request, Teacher $teacher): RedirectResponse|JsonResponse
    {
        DB::transaction(function () use ($request, $teacher) {
            // Handle avatar upload
            $avatarPath = $teacher->user->avatar;
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($teacher->user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($teacher->user->avatar)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($teacher->user->avatar);
                }
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'avatar' => $avatarPath, // Update avatar in users table
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $teacher->user->update($userData);

            $teacher->update([
                'employee_id' => $request->employee_id,
                'phone' => $request->phone,
                'qualification' => $request->qualification,
            ]);
        });

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Teacher updated successfully.']);
        }

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher updated successfully.');
    }

    /**
     * Remove the specified teacher from storage.
     */
    public function destroy(Teacher $teacher, Request $request): RedirectResponse|JsonResponse
    {
        DB::transaction(function () use ($teacher) {
            // Delete avatar if exists
            if ($teacher->user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($teacher->user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($teacher->user->avatar);
            }
            
            $user = $teacher->user;
            $teacher->delete();
            $user?->delete();
        });

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Teacher deleted successfully.']);
        }

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher deleted successfully.');
    }
}
