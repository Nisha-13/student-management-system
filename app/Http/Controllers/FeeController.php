<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeeController extends Controller
{
    /**
     * Display fee collection directory or JSON for DataTables.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $fees = Fee::with(['student.user', 'student.schoolClass', 'student.section'])->latest()->get();

            return response()->json([
                'data' => $fees->map(function ($fee) {
                    $due = $fee->amount - $fee->paid_amount;
                    return [
                        'id' => $fee->id,
                        'roll_number' => $fee->student->roll_number ?? 'N/A',
                        'student_name' => $fee->student->user->name ?? 'N/A',
                        'class_section' => ($fee->student->schoolClass->name ?? '') . ' - ' . ($fee->student->section->name ?? ''),
                        'title' => $fee->title,
                        'amount' => '$' . number_format($fee->amount, 2),
                        'paid_amount' => '$' . number_format($fee->paid_amount, 2),
                        'due_balance' => '$' . number_format(max(0, $due), 2),
                        'due_date' => $fee->due_date ? $fee->due_date->format('Y-m-d') : 'N/A',
                        'status' => match($fee->status) {
                            'paid' => '<span class="badge bg-success">Paid</span>',
                            'partial' => '<span class="badge bg-warning text-dark">Partial</span>',
                            default => '<span class="badge bg-danger">Unpaid</span>',
                        },
                        'actions' => '
                            <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-success edit-payment-btn" data-id="'.$fee->id.'" data-amount="'.$fee->amount.'" data-paid="'.$fee->paid_amount.'" data-status="'.$fee->status.'" title="Record Payment">
                                <i class="bi bi-cash-stack"></i> Pay
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-fee-btn" data-id="'.$fee->id.'" title="Delete Fee">
                                <i class="bi bi-trash"></i>
                            </button>
                            </div>
                        ',
                    ];
                }),
            ]);
        }

        $classes = SchoolClass::with(['sections', 'students.user'])->get();

        return view('fees.index', compact('classes'));
    }

    /**
     * Store new fee demand.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $fee = Fee::create([
            'student_id' => $validated['student_id'],
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'paid_amount' => 0,
            'due_date' => $validated['due_date'],
            'status' => 'unpaid',
            'remarks' => $validated['remarks'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Fee demand generated successfully.']);
        }

        return redirect()->route('fees.index')->with('success', 'Fee demand generated successfully.');
    }

    /**
     * Record payment for fee.
     */
    public function updatePayment(Request $request, Fee $fee): JsonResponse
    {
        $validated = $request->validate([
            'paid_amount' => ['required', 'numeric', 'min:0', 'max:' . $fee->amount],
        ]);

        $newPaid = $validated['paid_amount'];
        $status = 'unpaid';

        if ($newPaid >= $fee->amount) {
            $status = 'paid';
            $newPaid = $fee->amount;
        } elseif ($newPaid > 0) {
            $status = 'partial';
        }

        $fee->update([
            'paid_amount' => $newPaid,
            'status' => $status,
            'payment_date' => $status !== 'unpaid' ? now() : null,
        ]);

        return response()->json(['success' => true, 'message' => 'Payment updated successfully.']);
    }

    /**
     * Remove fee demand.
     */
    public function destroy(Fee $fee, Request $request): RedirectResponse|JsonResponse
    {
        $fee->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Fee record deleted successfully.']);
        }

        return redirect()->route('fees.index')->with('success', 'Fee record deleted successfully.');
    }
}
