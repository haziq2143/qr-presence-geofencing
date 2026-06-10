<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Utils\AttendanceHelper;
use App\Models\Attendance_Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendances = Attendance::join('classes', 'attendances.class_id', '=', 'classes.id')
            ->join('users', 'users.class_id', '=', 'classes.id')
            ->where('users.id', Auth::id())
            ->select('attendances.*')
            ->latest()
            ->get();
        $attendanceIds = $attendances->pluck('id');
        $record = Attendance_Record::whereIn('attendance_id', $attendanceIds)
            ->get();
        return view('record.index', compact('attendances', 'record'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('record.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'attendance_code' => 'required',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $inputCode = $validated['attendance_code'];
        $parts = explode('-', $inputCode);
        $baseCode = $parts[0];
        $qrTime = isset($parts[1]) ? (int)$parts[1] : null;

        $attendance = Attendance::where('attendance_code', $baseCode)->first();

        if (!$attendance) {
            return back()->withErrors(['attendance_code' => 'Kode absensi tidak valid atau sudah expired']);
        }

        if ($qrTime !== null) {
            $currentTime = (int)(time() / 30);
            if ($qrTime < ($currentTime - 1) || $qrTime > $currentTime) {
                return back()->withErrors(['attendance_code' => 'QR Code sudah kadaluarsa. Silakan scan QR yang baru.']);
            }
        }

        // Validate geolocation if provided and session is luring (offline)
        $distance = null;
        if ($attendance->type !== 'online' && $validated['latitude'] && $validated['longitude']) {
            if (!AttendanceHelper::isWithinSchoolRadius(
                (float)$validated['latitude'],
                (float)$validated['longitude']
            )) {
                $distance = AttendanceHelper::getDistanceFromSchool(
                    (float)$validated['latitude'],
                    (float)$validated['longitude']
                );
                return back()->withErrors([
                    'location' => AttendanceHelper::getDistanceErrorMessage($distance)
                ]);
            }
            $distance = AttendanceHelper::getDistanceFromSchool(
                (float)$validated['latitude'],
                (float)$validated['longitude']
            );
        }

        // Create attendance record with geolocation
        Attendance_Record::create([
            'user_id' => Auth::user()->id,
            'student_id' => Auth::user()->id,
            'attendance_id' => $attendance->id,
            'description' => 'Hadir',
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'distance_from_school' => $distance
        ]);

        return redirect('/home')->with('success', 'Absensi berhasil dicatat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
}
