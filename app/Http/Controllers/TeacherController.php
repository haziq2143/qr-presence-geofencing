<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('adminPage.teacher.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('adminPage.teacher.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'password' => 'required|min:8|max:30'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'teacher';

        User::create($validated);
        return redirect('/teachers');
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
        $teacher = User::where('id', $id)->first();
        return view('adminPage.teacher.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8|max:30'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $teacher->update($validated);
        return redirect('/teachers')->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);

        $classCount = \App\Models\Kelas::where('teacher_id', $id)->count();
        $subjectCount = \App\Models\Subject::where('teacher_id', $id)->count();

        if ($classCount > 0 || $subjectCount > 0) {
            return redirect('/teachers')->with('error', 'Guru tidak dapat dihapus karena masih mengajar kelas atau mata pelajaran.');
        }

        $teacher->delete();
        return redirect('/teachers')->with('success', 'Data guru berhasil dihapus.');
    }
}
