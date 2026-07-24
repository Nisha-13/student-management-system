<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TimetableController;
use Illuminate\Support\Facades\Route;

// -------------------------
// Guest-Only & Signed Routes
// -------------------------
Route::get('/portal-access/{user}', [LoginController::class, 'accessPortalViaLink'])
    ->name('portal.access')
    ->middleware('signed');

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm']);
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// -------------------------
// All Authenticated Routes
// -------------------------
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/users/{user}/resend-portal-link', [LoginController::class, 'resendAccessLink'])->name('users.resend-portal-link');

    // ----------------------------------------------------------
    // ADMIN ONLY Routes
    // ----------------------------------------------------------
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::resource('teachers', TeacherController::class);

        // Classes Management (Admin only)
        Route::get('/classes', [SchoolClassController::class, 'index'])->name('classes.index');
        Route::post('/classes', [SchoolClassController::class, 'store'])->name('classes.store');
        Route::put('/classes/{schoolClass}', [SchoolClassController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{schoolClass}', [SchoolClassController::class, 'destroy'])->name('classes.destroy');
        Route::post('/classes/{schoolClass}/sections', [SchoolClassController::class, 'storeSections'])->name('classes.sections.store');
        Route::get('/classes/{schoolClass}/sections-manage', [SchoolClassController::class, 'sections'])->name('classes.sections.list');
        Route::delete('/sections/{section}', [SchoolClassController::class, 'destroySection'])->name('sections.destroy');

        // Subjects Management (Admin only)
        Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
        Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
        Route::put('/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
        Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');
    });

    // ----------------------------------------------------------
    // ADMIN + TEACHER Routes (Shared Access)
    // ----------------------------------------------------------
    Route::middleware('role:teacher,admin')->group(function () {

        // Teacher Dashboard
        Route::get('/teacher/dashboard', [DashboardController::class, 'teacher'])->name('teacher.dashboard');

        // AJAX Helpers for dropdowns (used across forms)
        Route::get('/classes/{schoolClass}/sections', [StudentController::class, 'getSections'])->name('classes.sections');
        Route::get('/classes/{schoolClass}/subjects', [MarkController::class, 'getSubjects'])->name('classes.subjects');

        // Students CRUD
        Route::resource('students', StudentController::class);

        // Attendance
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/fetch', [AttendanceController::class, 'fetchStudents'])->name('attendance.fetch');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

        // Marks
        Route::get('/marks', [MarkController::class, 'index'])->name('marks.index');
        Route::post('/marks/fetch', [MarkController::class, 'fetchStudents'])->name('marks.fetch');
        Route::post('/marks', [MarkController::class, 'store'])->name('marks.store');

        // Fee Management
        Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
        Route::post('/fees', [FeeController::class, 'store'])->name('fees.store');
        Route::put('/fees/{fee}/payment', [FeeController::class, 'updatePayment'])->name('fees.payment');
        Route::delete('/fees/{fee}', [FeeController::class, 'destroy'])->name('fees.destroy');

        // Timetable
        Route::get('/timetables', [TimetableController::class, 'index'])->name('timetables.index');
        Route::post('/timetables/grid', [TimetableController::class, 'fetchGrid'])->name('timetables.grid');
        Route::post('/timetables', [TimetableController::class, 'store'])->name('timetables.store');
        Route::delete('/timetables/{timetable}', [TimetableController::class, 'destroy'])->name('timetables.destroy');
    });

    // ----------------------------------------------------------
    // Report Card (Admin + Teacher + Student)
    // ----------------------------------------------------------
    Route::get('/students/{student}/report-card', [ReportCardController::class, 'show'])
        ->name('students.report-card')
        ->middleware('role:admin,teacher,student');

    // ----------------------------------------------------------
    // STUDENT ONLY Routes
    // ----------------------------------------------------------
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'student'])->name('dashboard');
    });
});
