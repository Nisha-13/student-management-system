<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Fee;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Admin Dashboard.
     */
    public function admin(): View
    {
        $stats = [
            'students' => Student::count(),
            'teachers' => Teacher::count(),
            'classes' => SchoolClass::count(),
            'subjects' => Subject::count(),
            'totalFees' => Fee::sum('amount'),
            'collectedFees' => Fee::sum('paid_amount'),
            'unpaidFees' => Fee::where('status', 'unpaid')->count(),
            'todayAttendance' => Attendance::whereDate('date', today())->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Teacher Dashboard.
     */
    public function teacher(): View
    {
        return view('teacher.dashboard');
    }

    /**
     * Student Dashboard / Portal.
     */
    public function student(): View
    {
        $student = auth()->user()->student;

        if ($student) {
            $student->load([
                'schoolClass',
                'section',
                'attendances',
                'marks.subject',
                'fees',
            ]);
        }

        return view('student.dashboard', compact('student'));
    }
}
