<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RecordController;

Route::get('/', [AuthController::class, 'login']);
Route::post('/', [AuthController::class, 'authtenticate']);
Route::get('/logout', [AuthController::class, 'logout']);




Route::middleware(['admin'])->group(function () {
    Route::get('/admin', function () {
        $studentCount = \App\Models\User::where('role', 'student')->count();
        $teacherCount = \App\Models\User::where('role', 'teacher')->count();
        $classCount = \App\Models\Kelas::count();
        $subjectCount = \App\Models\Subject::count();
        return view('adminPage.admin', compact('studentCount', 'teacherCount', 'classCount', 'subjectCount'));
    });
    Route::get('/exporth', [ExportController::class, 'export_excel']);
    Route::get('/students/upload', [StudentController::class, 'upload']);
    Route::post('/students/import', [StudentController::class, 'import']);
    Route::resource('/students', StudentController::class);
    Route::resource('/teachers', TeacherController::class);
    Route::resource('/classes', ClassController::class);
    Route::resource('/subjects', SubjectController::class);
});

Route::middleware(['teacher'])->group(function () {
    Route::get('/attendances/recap', [AttendanceController::class, 'recapForm'])->name('attendances.recap.form');
    Route::get('/attendances/recap/download', [AttendanceController::class, 'downloadRecap'])->name('attendances.recap.download');
    Route::resource('/attendances',  AttendanceController::class);
    Route::get('/api/attendance/{attendance}/qr', [AttendanceController::class, 'generateQr'])->name('attendance.qr');
});

Route::get('/api/attendance/type/{code}', [AttendanceController::class, 'getType']);
Route::resource('/record',  RecordController::class)->middleware('auth');
Route::resource('/home', HomeController::class, ['except' => 'create', 'except' => 'store'])->middleware('auth');
Route::get('/home/create/{attendances_record}', [HomeController::class, 'create'])->middleware('auth');
Route::post('/home/store/{attendances_record}', [HomeController::class, 'store'])->middleware('auth');
