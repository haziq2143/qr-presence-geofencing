<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::query()->get();
        return view('adminPage.subject.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('adminPage.subject.create', compact('teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required',
            'teacher_id' => 'required'
        ]);

        Subject::create($validated);
        return redirect('/subjects');
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
        $subject = Subject::findOrFail($id);
        $teachers = User::where('role', 'teacher')->get();
        return view('adminPage.subject.edit', compact('subject', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'subject' => 'required|max:255',
            'teacher_id' => 'required|exists:users,id'
        ]);

        $subject->update($validated);

        return redirect('/subjects')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subject = Subject::findOrFail($id);

        $attendanceCount = \App\Models\Attendance::where('subject_id', $id)->count();

        if ($attendanceCount > 0) {
            return redirect('/subjects')->with('error', 'Mata pelajaran tidak dapat dihapus karena sudah memiliki data absensi.');
        }

        $subject->delete();

        return redirect('/subjects')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
