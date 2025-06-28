<?php

use App\Models\Group;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    return Group::whereHas('activeStudyYear')->get();
});
