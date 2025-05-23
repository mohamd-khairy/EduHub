<?php

use App\Http\Controllers\GeneralController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([], function () {

    Route::get('student', [GeneralController::class, 'index']);
    Route::get('course', [GeneralController::class, 'index']);
    Route::get('teacher', [GeneralController::class, 'index']);
    Route::get('parent', [GeneralController::class, 'index']);
    Route::get('exam', [GeneralController::class, 'index']);
    Route::get('attendance', [GeneralController::class, 'index']);
    Route::get('payment', [GeneralController::class, 'index']);
    Route::get('examResult', [GeneralController::class, 'index']);
    Route::get('teacherAttendance', [GeneralController::class, 'index']);
});
