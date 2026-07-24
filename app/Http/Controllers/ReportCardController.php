<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportCardController extends Controller
{
    /**
     * Display printable report card view for a student.
     */
    public function show(Student $student): View
    {
        $student->load([
            'user',
            'schoolClass',
            'section',
            'attendances',
            'marks.subject',
            'fees',
        ]);

        $totalMarks = $student->marks->sum('marks_obtained');
        $maxMarksTotal = $student->marks->sum('max_marks');
        $percentage = $maxMarksTotal > 0 ? round(($totalMarks / $maxMarksTotal) * 100, 2) : 0;

        $grade = match(true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B',
            $percentage >= 60 => 'C',
            $percentage >= 50 => 'D',
            default => 'F',
        };

        $totalAtt = $student->attendances->count();
        $presentAtt = $student->attendances->where('status', 'present')->count();
        $attPercentage = $totalAtt > 0 ? round(($presentAtt / $totalAtt) * 100, 1) : 100;

        return view('reports.report_card', compact(
            'student',
            'totalMarks',
            'maxMarksTotal',
            'percentage',
            'grade',
            'totalAtt',
            'presentAtt',
            'attPercentage'
        ));
    }
}
