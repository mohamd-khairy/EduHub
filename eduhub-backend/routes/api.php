<?php

use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\ExamController;
use App\Http\Controllers\API\GeneralController;
use App\Http\Controllers\API\GroupController;
use App\Http\Controllers\API\ParentController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ResultController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\TeacherController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([], function () {

    Route::group(['prefix' => 'group'], function () {
        Route::get('/', [GroupController::class, 'index']);
        Route::get('/all', [GroupController::class, 'All']);
        Route::post('', [GroupController::class, 'store']);
        Route::put('{id}', [GroupController::class, 'update']);
        Route::post('/delete-all', [GroupController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'course'], function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::get('/all', [CourseController::class, 'All']);
        Route::post('', [CourseController::class, 'store']);
        Route::put('{id}', [CourseController::class, 'update']);
        Route::post('/delete-all', [CourseController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'teacher'], function () {
        Route::get('/', [TeacherController::class, 'index']);
        Route::get('/all', [TeacherController::class, 'All']);
        Route::post('', [TeacherController::class, 'store']);
        Route::put('{id}', [TeacherController::class, 'update']);
        Route::post('/delete-all', [TeacherController::class, 'deleteAll']);
    });

    Route::group(['prefix' => 'user'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/all', [UserController::class, 'All']);
        Route::post('', [UserController::class, 'store']);
        Route::put('{id}', [UserController::class, 'update']);
        Route::post('/delete-all', [UserController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'student'], function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::get('/all', [StudentController::class, 'All']);
        Route::post('', [StudentController::class, 'store']);
        Route::put('{id}', [StudentController::class, 'update']);
        Route::post('/delete-all', [StudentController::class, 'deleteAll']);
    });

    Route::group(['prefix' => 'parentModel'], function () {
        Route::get('/', [ParentController::class, 'index']);
        Route::get('/all', [ParentController::class, 'All']);
        Route::post('', [ParentController::class, 'store']);
        Route::put('{id}', [ParentController::class, 'update']);
        Route::post('/delete-all', [ParentController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'exam'], function () {
        Route::get('/', [ExamController::class, 'index']);
        Route::get('/all', [ExamController::class, 'All']);
        Route::post('', [ExamController::class, 'store']);
        Route::put('{id}', [ExamController::class, 'update']);
        Route::post('/delete-all', [ExamController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'examResult'], function () {
        Route::get('/', [ResultController::class, 'index']);
        Route::get('/all', [ResultController::class, 'All']);
        Route::post('', [ResultController::class, 'store']);
        Route::put('{id}', [ResultController::class, 'update']);
        Route::post('/delete-all', [ResultController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'attendance'], function () {
        Route::get('/', [AttendanceController::class, 'index']);
        Route::get('/all', [AttendanceController::class, 'All']);
        Route::post('', [AttendanceController::class, 'store']);
        Route::put('{id}', [AttendanceController::class, 'update']);
        Route::post('/delete-all', [AttendanceController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'payment'], function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::get('/all', [PaymentController::class, 'All']);
        Route::post('', [PaymentController::class, 'store']);
        Route::put('{id}', [PaymentController::class, 'update']);
        Route::post('/delete-all', [PaymentController::class, 'deleteAll']);
    });
});
