<?php

use App\Http\Controllers\API\StudentController;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\Student;
use App\Models\StudyYear;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    // return Group::whereHas('activeStudyYear')->get();
    // return Student::latest()
    //     ->where(function ($q) {
    //         $q->whereDoesntHave('enrollments')
    //             ->orWhereHas('enrollments.group', fn($q) => $q->where('study_year_id', StudyYear::current()));
    //     })
    //     ->get();

    // return get_day_name_by_date('2025-07-21');

    return $data = Group::with('schedules', 'activeStudyYear')
        // ->whereHas('schedules', fn($q) => $q->where('day', (string) get_day_name_by_date('2025-07-21')))
        ->get();
});


Route::get('/student-card/{id}/pdf', [StudentController::class, 'generatePDF'])->name('student-card.pdf');
Route::get('/student-card/{id}/view', [StudentController::class, 'viewgeneratePDF'])->name('student-card.pdf');
