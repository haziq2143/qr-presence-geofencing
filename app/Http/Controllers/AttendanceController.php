<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Subject;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendances = Attendance::query()->get();
        return view('attendance.index', compact('attendances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = Kelas::query()->get();
        $subjects = Subject::where('teacher_id', Auth::user()->id)->get();
        return view('attendance.create', compact('classes', 'subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required',
            'class_id' => 'required',
            'attendance_date' => 'required',
            'type' => 'nullable|string|in:online,offline'
        ]);

        $validated['attendance_code'] = str()->random(6);
        $validated['type'] = $validated['type'] ?? 'offline';

        Attendance::create($validated);

        return redirect('/attendances');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $attendance = Attendance::where('id', $id)->first();
        $qrContent = $attendance->attendance_code . '-' . (int)(time() / 30);
        $qr = QrCode::size(200)->generate($qrContent);
        return view('attendance.show', compact('attendance', 'qr'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Generate dynamic QR code for attendance (expires every 30 seconds)
     */
    public function generateQr(string $id)
    {
        $attendance = Attendance::findOrFail($id);

        // Generate dynamic QR code (can be same as attendance code or with timestamp)
        $qrContent = $attendance->attendance_code . '-' . (int)(time() / 30);
        $qr = QrCode::size(200)->generate($qrContent);

        return response()->json([
            'qr' => (string) $qr,
            'code' => $attendance->attendance_code,
            'timestamp' => now(),
            'expires_in' => 30 // seconds
        ]);
    }

    public function recapForm(Request $request)
    {
        $classes = Kelas::query()->get();
        $subjects = Subject::where('teacher_id', Auth::user()->id)->get();

        $selectedClass = null;
        $selectedSubject = null;
        $startDate = null;
        $endDate = null;
        $attendances = collect();
        $matrix = collect();

        if ($request->filled('class_id') && $request->filled('subject_id')) {
            $selectedClass = Kelas::findOrFail($request->class_id);
            $selectedSubject = Subject::where('id', $request->subject_id)
                ->where('teacher_id', Auth::user()->id)
                ->firstOrFail();

            $dateRange = $this->calculateDateRange($request);
            $startDate = $dateRange['start'];
            $endDate = $dateRange['end'];

            // Fetch attendances for class, subject in the date range
            $attendances = Attendance::where('class_id', $selectedClass->id)
                ->where('subject_id', $selectedSubject->id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->orderBy('attendance_date', 'asc')
                ->get();

            // Fetch students in the class
            $students = User::where('class_id', $selectedClass->id)
                ->where('role', 'student')
                ->orderBy('name', 'asc')
                ->get();

            // Build matrix
            $matrix = [];
            foreach ($students as $student) {
                $row = [
                    'student' => $student,
                    'attendance' => [],
                    'hadir' => 0,
                    'sakit' => 0,
                    'izin' => 0,
                    'alpha' => 0,
                ];

                foreach ($attendances as $att) {
                    $record = $att->attendance_record->firstWhere('student_id', $student->id);
                    $status = $record ? $record->description : 'Alpha'; // default to Alpha if not present
                    $row['attendance'][$att->id] = $status;

                    if ($status == 'Hadir') $row['hadir']++;
                    elseif ($status == 'Sakit') $row['sakit']++;
                    elseif ($status == 'Izin') $row['izin']++;
                    elseif ($status == 'Alpha') $row['alpha']++;
                }

                $totalSessions = count($attendances);
                $row['percentage'] = $totalSessions > 0 ? round(($row['hadir'] / $totalSessions) * 100, 1) : 0;

                $matrix[] = $row;
            }
        }

        return view('attendance.recap', compact(
            'classes', 'subjects', 'selectedClass', 'selectedSubject',
            'startDate', 'endDate', 'attendances', 'matrix'
        ));
    }

    public function downloadRecap(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'subject_id' => 'required',
        ]);

        $selectedClass = Kelas::findOrFail($request->class_id);
        $selectedSubject = Subject::where('id', $request->subject_id)
            ->where('teacher_id', Auth::user()->id)
            ->firstOrFail();

        $dateRange = $this->calculateDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        $attendances = Attendance::where('class_id', $selectedClass->id)
            ->where('subject_id', $selectedSubject->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->orderBy('attendance_date', 'asc')
            ->get();

        $students = User::where('class_id', $selectedClass->id)
            ->where('role', 'student')
            ->orderBy('name', 'asc')
            ->get();

        $matrix = [];
        foreach ($students as $student) {
            $row = [
                'student' => $student,
                'attendance' => [],
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpha' => 0,
            ];

            foreach ($attendances as $att) {
                $record = $att->attendance_record->firstWhere('student_id', $student->id);
                $status = $record ? $record->description : 'Alpha';
                $row['attendance'][$att->id] = $status;

                if ($status == 'Hadir') $row['hadir']++;
                elseif ($status == 'Sakit') $row['sakit']++;
                elseif ($status == 'Izin') $row['izin']++;
                elseif ($status == 'Alpha') $row['alpha']++;
            }

            $totalSessions = count($attendances);
            $row['percentage'] = $totalSessions > 0 ? round(($row['hadir'] / $totalSessions) * 100, 1) : 0;

            $matrix[] = $row;
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('attendance.recap_pdf', compact(
            'selectedClass', 'selectedSubject', 'startDate', 'endDate', 'attendances', 'matrix'
        ));

        $filename = 'recap_absensi_' . strtolower(str_replace(' ', '_', $selectedClass->class)) . '_' . strtolower(str_replace(' ', '_', $selectedSubject->subject)) . '_' . now()->format('YmdHis') . '.pdf';

        return $pdf->download($filename);
    }

    private function calculateDateRange(Request $request)
    {
        $periodType = $request->input('period_type', 'weekly');
        $startDate = null;
        $endDate = null;

        if ($periodType == 'weekly' && $request->filled('week')) {
            $parts = explode('-W', $request->week);
            if (count($parts) === 2) {
                $year = (int)$parts[0];
                $week = (int)$parts[1];
                $startDate = \Carbon\Carbon::now()->setISODate($year, $week)->startOfWeek();
                $endDate = \Carbon\Carbon::now()->setISODate($year, $week)->endOfWeek();
            }
        } elseif ($periodType == 'monthly' && $request->filled('month')) {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $year = (int)$parts[0];
                $month = (int)$parts[1];
                $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endDate = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();
            }
        } elseif ($periodType == 'semesterly' && $request->filled('semester_year') && $request->filled('semester')) {
            $year = (int)$request->semester_year;
            $semester = $request->semester;
            if ($semester == 'ganjil') {
                $startDate = \Carbon\Carbon::createFromDate($year, 7, 1)->startOfDay();
                $endDate = \Carbon\Carbon::createFromDate($year, 12, 31)->endOfDay();
            } else {
                $startDate = \Carbon\Carbon::createFromDate($year, 1, 1)->startOfDay();
                $endDate = \Carbon\Carbon::createFromDate($year, 6, 30)->endOfDay();
            }
        }

        if (!$startDate || !$endDate) {
            $startDate = now()->startOfWeek();
            $endDate = now()->endOfWeek();
        }

        return ['start' => $startDate, 'end' => $endDate];
    }

    public function getType(string $code)
    {
        $parts = explode('-', $code);
        $baseCode = $parts[0];
        $attendance = Attendance::where('attendance_code', $baseCode)->first();
        return response()->json([
            'type' => $attendance ? $attendance->type : 'offline'
        ]);
    }
}
