<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Utils\AttendanceHelper;
use Illuminate\Http\Request;
use App\Models\Attendance_Record;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendances = Attendance_Record::where('user_id', Auth::user()->id)->get();
        return view('home.home', compact('attendances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id)
    {
        $attendance = Attendance::findOrFail($id);
        $users = User::join('attendances', 'users.class_id', '=', 'attendances.class_id')
            ->where('attendances.id', $id)
            ->select('users.*')
            ->get();

        return view('home.create', compact('users', 'id', 'attendance'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        $validated = $request->validate([
            'students' => 'required',
            'descriptions' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'distance' => 'required|numeric',
        ]);

        $attendance = Attendance::findOrFail($id);

        $latitude = (float)$validated['latitude'];
        $longitude = (float)$validated['longitude'];
        $distance = (float)$validated['distance'];

        if ($attendance->type !== 'online') {
            if (!AttendanceHelper::isWithinSchoolRadius($latitude, $longitude)) {
                return back()->withErrors([
                    'location' => AttendanceHelper::getDistanceErrorMessage($distance)
                ])->withInput();
            }
        }

        // Create records with geolocation data
        foreach ($request->students as $index => $student_id) {
            Attendance_Record::create([
                'user_id' => $student_id,
                'student_id' => $student_id,
                'description' => $request->descriptions[$index],
                'attendance_id' => $id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'distance_from_school' => $distance
            ]);
        }

        return redirect('/attendances')->with('success', 'Absensi berhasil dicatat');
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
