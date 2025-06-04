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
        Route::get('/all', [GroupController::class, 'All']);
        Route::post('/delete-all', [GroupController::class, 'deleteAll']);
        Route::get('/', [GroupController::class, 'index']);
        Route::post('', [GroupController::class, 'store']);
        Route::get('{id}', [GroupController::class, 'show']);
        Route::put('{id}', [GroupController::class, 'update']);
    });
    Route::group(['prefix' => 'course'], function () {
        Route::get('/all', [CourseController::class, 'All']);
        Route::post('/delete-all', [CourseController::class, 'deleteAll']);
        Route::get('/', [CourseController::class, 'index']);
        Route::post('', [CourseController::class, 'store']);
        Route::get('{id}', [CourseController::class, 'show']);
        Route::put('{id}', [CourseController::class, 'update']);
    });
    Route::group(['prefix' => 'teacher'], function () {
        Route::get('/all', [TeacherController::class, 'All']);
        Route::post('/delete-all', [TeacherController::class, 'deleteAll']);
        Route::get('/', [TeacherController::class, 'index']);
        Route::post('', [TeacherController::class, 'store']);
        Route::get('{id}', [TeacherController::class, 'update']);
        Route::put('{id}', [TeacherController::class, 'update']);
    });

    Route::group(['prefix' => 'user'], function () {
        Route::get('/all', [UserController::class, 'All']);
        Route::post('/delete-all', [UserController::class, 'deleteAll']);
        Route::get('/', [UserController::class, 'index']);
        Route::post('', [UserController::class, 'store']);
        Route::get('{id}', [UserController::class, 'show']);
        Route::put('{id}', [UserController::class, 'update']);
    });
    Route::group(['prefix' => 'student'], function () {
        Route::get('/all', [StudentController::class, 'All']);
        Route::post('/delete-all', [StudentController::class, 'deleteAll']);
        Route::get('/', [StudentController::class, 'index']);
        Route::post('', [StudentController::class, 'store']);
        Route::get('{id}', [StudentController::class, 'show']);
        Route::put('{id}', [StudentController::class, 'update']);
    });

    Route::group(['prefix' => 'parentModel'], function () {
        Route::get('/all', [ParentController::class, 'All']);
        Route::post('/delete-all', [ParentController::class, 'deleteAll']);
        Route::get('/', [ParentController::class, 'index']);
        Route::post('', [ParentController::class, 'store']);
        Route::get('{id}', [ParentController::class, 'show']);
        Route::put('{id}', [ParentController::class, 'update']);
    });
    Route::group(['prefix' => 'exam'], function () {
        Route::get('/all', [ExamController::class, 'All']);
        Route::post('/delete-all', [ExamController::class, 'deleteAll']);
        Route::get('/', [ExamController::class, 'index']);
        Route::post('', [ExamController::class, 'store']);
        Route::get('{id}', [ExamController::class, 'show']);
        Route::put('{id}', [ExamController::class, 'update']);
    });
    Route::group(['prefix' => 'examResult'], function () {
        Route::get('/all', [ResultController::class, 'All']);
        Route::post('/delete-all', [ResultController::class, 'deleteAll']);
        Route::get('/', [ResultController::class, 'index']);
        Route::post('', [ResultController::class, 'store']);
        Route::get('{id}', [ResultController::class, 'show']);
        Route::put('{id}', [ResultController::class, 'update']);
    });
    Route::group(['prefix' => 'attendance'], function () {
        Route::get('/all', [AttendanceController::class, 'All']);
        Route::post('/delete-all', [AttendanceController::class, 'deleteAll']);
        Route::get('/', [AttendanceController::class, 'index']);
        Route::post('', [AttendanceController::class, 'store']);
        Route::get('{id}', [AttendanceController::class, 'show']);
        Route::put('{id}', [AttendanceController::class, 'update']);
    });
    Route::group(['prefix' => 'payment'], function () {
        Route::get('/all', [PaymentController::class, 'All']);
        Route::post('/delete-all', [PaymentController::class, 'deleteAll']);
        Route::get('/', [PaymentController::class, 'index']);
        Route::post('', [PaymentController::class, 'store']);
        Route::get('{id}', [PaymentController::class, 'show']);
        Route::put('{id}', [PaymentController::class, 'update']);
    });
});
