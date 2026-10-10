<?php

use Illuminate\Support\Facades\Route;
use Modules\JobsAndObituary\Controllers\JobsAndObituaryController;

Route::get('/jobs', [JobsAndObituaryController::class, 'publicJobs']);
Route::get('/obituaries', [JobsAndObituaryController::class, 'publicObituaries']);
