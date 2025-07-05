<?php

use App\Http\Controllers\API\StudentController;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\Student;
use App\Models\StudyYear;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/student-card/{id}/pdf', [StudentController::class, 'generatePDF'])->name('student-card.pdf');
Route::get('/student-card/{id}/view', [StudentController::class, 'viewgeneratePDF'])->name('student-card.pdf');
