<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $classes = Kelas::query()->get();
        return view('adminPage.classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('adminPage.classes.create', compact('teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class' => 'required',
            'teacher_id' => 'required|unique:classes,teacher_id'
        ]);

        Kelas::create($validated);
        return redirect('/classes');
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
        $class = Kelas::findOrFail($id);
        $teachers = User::where('role', 'teacher')->get();
        return view('adminPage.classes.edit', compact('class', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $class = Kelas::findOrFail($id);

        $validated = $request->validate([
            'class' => 'required|max:255',
            'teacher_id' => 'required|unique:classes,teacher_id,' . $id
        ]);

        $class->update($validated);

        return redirect('/classes')->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $class = Kelas::findOrFail($id);

        $studentCount = User::where('class_id', $id)->count();
        $attendanceCount = \App\Models\Attendance::where('class_id', $id)->count();

        if ($studentCount > 0 || $attendanceCount > 0) {
            return redirect('/classes')->with('error', 'Kelas tidak dapat dihapus karena masih memiliki siswa atau data absensi.');
        }

        $class->delete();

        return redirect('/classes')->with('success', 'Kelas berhasil dihapus.');
    }
}
