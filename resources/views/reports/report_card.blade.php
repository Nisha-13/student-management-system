<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card - {{ $student->user->name }}</title>
    <!-- Fonts: Inter + Cursive Signature Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Great+Vibes&family=Dancing+Script:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- html2pdf.js Bundle for direct PDF download -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .report-card-container {
            max-width: 850px;
            margin: 40px auto;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .header-logo {
            font-size: 2.5rem;
            color: #2563eb;
        }
        .report-header {
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .info-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }
        .info-value {
            font-weight: 600;
            font-size: 1rem;
        }
        .grade-badge {
            font-size: 2rem;
            font-weight: 700;
            padding: 10px 25px;
            border-radius: 12px;
            background: #eff6ff;
            color: #2563eb;
            border: 2px solid #bfdbfe;
        }

        /* Signatures & Seal Styling */
        .signature-script-1 {
            font-family: 'Great Vibes', cursive;
            font-size: 2.2rem;
            color: #1e3a8a;
            line-height: 1;
            transform: rotate(-3deg);
            display: inline-block;
        }
        .signature-script-2 {
            font-family: 'Dancing Script', cursive;
            font-size: 1.8rem;
            font-weight: 700;
            color: #065f46;
            line-height: 1;
            transform: rotate(-2deg);
            display: inline-block;
        }
        .official-seal {
            width: 70px;
            height: 70px;
            border: 3px double #1e3a8a;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto 5px auto;
            color: #1e3a8a;
            font-size: 0.55rem;
            font-weight: 700;
            text-transform: uppercase;
            background: #eff6ff;
            letter-spacing: 0.5px;
            box-shadow: inset 0 0 0 2px #bfdbfe;
        }

        @media print {
            body {
                background: #fff;
            }
            .report-card-container {
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 20px;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-end my-3 no-print d-flex flex-wrap justify-content-end align-items-center gap-2">
            <button type="button" onclick="downloadPDF()" class="btn btn-success rounded-pill px-4 fw-semibold">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download PDF
            </button>
            <button type="button" onclick="window.print()" class="btn btn-primary rounded-pill px-4 fw-semibold">
                <i class="bi bi-printer-fill me-1"></i> Print Report
            </button>
            <a href="{{ auth()->user()->isStudent() ? route('student.dashboard') : (auth()->user()->isAdmin() ? route('admin.dashboard') : route('teacher.dashboard')) }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </a>
            <button type="button" onclick="if (window.history.length > 1 && document.referrer) { window.history.back(); } else { window.close(); }" class="btn btn-outline-dark rounded-pill px-3">
                <i class="bi bi-x-lg me-1"></i> Close
            </button>
        </div>

        <div id="reportCardContent" class="report-card-container">
            <!-- Header -->
            <div class="report-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-mortarboard-fill header-logo"></i>
                    <div>
                        <h2 class="fw-bold m-0 text-dark">SPRINGFIELD ACADEMY</h2>
                        <span class="text-muted small">Official Academic Report Card</span>
                    </div>
                </div>
                <div class="text-end">
                    <div class="grade-badge">{{ $grade }}</div>
                    <small class="text-muted d-block mt-1">Overall Grade</small>
                </div>
            </div>

            <!-- Student Profile Information -->
            <div class="row g-3 mb-4 p-3 bg-light rounded-3 align-items-center">
                <div class="col-md-2 text-center text-md-start">
                    <img src="{{ $student->avatar_url }}" class="rounded-circle border shadow-sm" width="75" height="75" style="object-fit:cover;" alt="{{ $student->user->name }}">
                </div>
                <div class="col-md-10">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <span class="info-label">Student Name</span>
                            <div class="info-value">{{ $student->user->name }}</div>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="info-label">Roll Number</span>
                            <div class="info-value">{{ $student->roll_number }}</div>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="info-label">Class & Section</span>
                            <div class="info-value">{{ $student->schoolClass->name ?? 'N/A' }} ({{ $student->section->name ?? 'N/A' }})</div>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="info-label">Gender / DOB</span>
                            <div class="info-value text-capitalize">{{ $student->gender }} / {{ optional($student->dob)->format('Y-m-d') ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Examination Marks -->
            <h5 class="fw-bold mb-3"><i class="bi bi-journal-bookmark text-primary me-2"></i> Examination Results</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Subject</th>
                            <th>Exam Title</th>
                            <th class="text-center">Marks Obtained</th>
                            <th class="text-center">Maximum Marks</th>
                            <th class="text-center">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->marks as $mark)
                            @php
                                $subPerc = $mark->max_marks > 0 ? round(($mark->marks_obtained / $mark->max_marks) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $mark->subject->name ?? 'N/A' }}</td>
                                <td>{{ $mark->exam_name }}</td>
                                <td class="text-center fw-bold text-success">{{ $mark->marks_obtained }}</td>
                                <td class="text-center">{{ $mark->max_marks }}</td>
                                <td class="text-center">{{ $subPerc }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No exam marks logged.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2">TOTAL ACADEMIC SCORE</td>
                            <td class="text-center text-primary fs-5">{{ $totalMarks }}</td>
                            <td class="text-center fs-5">{{ $maxMarksTotal }}</td>
                            <td class="text-center text-primary fs-5">{{ $percentage }}%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Attendance & Attendance Breakdown -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card p-3 border-0 bg-light">
                        <h6 class="fw-bold mb-2"><i class="bi bi-calendar-check text-warning me-1"></i> Attendance Record</h6>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted">Attendance Percentage</span>
                            <span class="fw-bold">{{ $attPercentage }}%</span>
                        </div>
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: {{ $attPercentage }}%"></div>
                        </div>
                        <small class="text-muted">Total Academic Days: {{ $totalAtt }} | Days Present: {{ $presentAtt }}</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card p-3 border-0 bg-light">
                        <h6 class="fw-bold mb-2"><i class="bi bi-cash-stack text-success me-1"></i> Financial Status Summary</h6>
                        @php
                            $totalFeeAmount = $student->fees->sum('amount');
                            $totalFeePaid = $student->fees->sum('paid_amount');
                            $feeDue = max(0, $totalFeeAmount - $totalFeePaid);
                        @endphp
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Total Billed Fees:</span>
                            <span class="fw-semibold">${{ number_format($totalFeeAmount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Total Fees Paid:</span>
                            <span class="fw-semibold text-success">${{ number_format($totalFeePaid, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small fw-bold text-danger border-top pt-1 mt-1">
                            <span>Outstanding Balance:</span>
                            <span>${{ number_format($feeDue, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Authentic Signatures & Seal Section -->
            <div class="row text-center mt-5 pt-4 border-top align-items-end g-4">
                <div class="col-12 col-sm-4">
                    <div class="signature-box mb-2" style="min-height: 45px;">
                        <span class="signature-script-1">Sarah Connor</span>
                    </div>
                    <div class="border-top border-secondary w-75 mx-auto pt-1 small fw-semibold text-muted">Class Teacher Signature</div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="signature-box mb-2" style="min-height: 45px;">
                        <span class="signature-script-2">Dr. Robert Ford</span>
                    </div>
                    <div class="border-top border-secondary w-75 mx-auto pt-1 small fw-semibold text-muted">Principal Signature</div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="official-seal">
                        <i class="bi bi-patch-check-fill fs-6 text-primary"></i>
                        <span>SEAL</span>
                    </div>
                    <div class="border-top border-secondary w-75 mx-auto pt-1 small fw-semibold text-muted">
                        School Seal & Date<br>
                        <span class="text-dark small">{{ date('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function downloadPDF() {
            var element = document.getElementById('reportCardContent');
            var opt = {
                margin:       8,
                filename:     'Report_Card_{{ Str::slug($student->user->name) }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, logging: false },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
