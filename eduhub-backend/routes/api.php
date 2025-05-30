<?php

use App\Http\Controllers\GeneralController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([], function () {

    Route::group(['prefix' => 'group'], function () {
        Route::get('/', [GeneralController::class, 'index']);
        Route::post('', [GeneralController::class,'store']);
        Route::put('{id}', [GeneralController::class,'update']);
        Route::get('/all', [GeneralController::class, 'All']);
        Route::post('/delete-all', [GeneralController::class, 'deleteAll']);
    });
     Route::group(['prefix' => 'course'], function () {
        Route::get('/', [GeneralController::class, 'index']);
        Route::post('', [GeneralController::class,'store']);
        Route::put('{id}', [GeneralController::class,'update']);
        Route::get('/all', [GeneralController::class, 'All']);
        Route::post('/delete-all', [GeneralController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'teacher'], function () {
        Route::get('/', [GeneralController::class, 'index']);
        Route::post('', [GeneralController::class,'store']);
        Route::put('{id}', [GeneralController::class,'update']);
        Route::get('/all', [GeneralController::class, 'All']);
        Route::post('/delete-all', [GeneralController::class, 'deleteAll']);
    });

    Route::group(['prefix' => 'user'], function () {
        Route::get('/', [GeneralController::class, 'index']);
        Route::post('', [GeneralController::class,'store']);
        Route::put('{id}', [GeneralController::class,'update']);
        Route::get('/all', [GeneralController::class, 'All']);
        Route::post('/delete-all', [GeneralController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'student'], function () {
        Route::get('/', [GeneralController::class, 'index']);
        Route::post('', [GeneralController::class,'store']);
        Route::put('{id}', [GeneralController::class,'update']);
        Route::get('/all', [GeneralController::class, 'All']);
        Route::post('/delete-all', [GeneralController::class, 'deleteAll']);
    });
   
    Route::group(['prefix' => 'parentModel'], function () {
        Route::get('/', [GeneralController::class, 'index']);
        Route::post('', [GeneralController::class,'store']);
        Route::put('{id}', [GeneralController::class,'update']);
        Route::get('/all', [GeneralController::class, 'All']);
        Route::post('/delete-all', [GeneralController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'exam'], function () {
        Route::get('/', [GeneralController::class, 'index']);
        Route::post('', [GeneralController::class,'store']);
        Route::put('{id}', [GeneralController::class,'update']);
        Route::get('/all', [GeneralController::class, 'All']);
        Route::post('/delete-all', [GeneralController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'parentModel'], function () {
        Route::get('/', [GeneralController::class, 'index']);
        Route::post('', [GeneralController::class,'store']);
        Route::put('{id}', [GeneralController::class,'update']);
        Route::get('/all', [GeneralController::class, 'All']);
        Route::post('/delete-all', [GeneralController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'examResult'], function () {
        Route::get('/', [GeneralController::class, 'index']);
        Route::post('', [GeneralController::class,'store']);
        Route::put('{id}', [GeneralController::class,'update']);
        Route::get('/all', [GeneralController::class, 'All']);
        Route::post('/delete-all', [GeneralController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'attendance'], function () {
        Route::get('/', [GeneralController::class, 'index']);
        Route::post('', [GeneralController::class,'store']);
        Route::put('{id}', [GeneralController::class,'update']);
        Route::get('/all', [GeneralController::class, 'All']);
        Route::post('/delete-all', [GeneralController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'payment'], function () {
        Route::get('/', [GeneralController::class, 'index']);
        Route::post('', [GeneralController::class,'store']);
        Route::put('{id}', [GeneralController::class,'update']);
        Route::get('/all', [GeneralController::class, 'All']);
        Route::post('/delete-all', [GeneralController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'teacherAttendance'], function () {
        Route::get('/', [GeneralController::class, 'index']);
        Route::post('', [GeneralController::class,'store']);
        Route::put('{id}', [GeneralController::class,'update']);
        Route::get('/all', [GeneralController::class, 'All']);
        Route::post('/delete-all', [GeneralController::class, 'deleteAll']);
    });
});
