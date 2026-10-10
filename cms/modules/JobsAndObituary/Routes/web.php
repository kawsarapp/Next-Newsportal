<?php

use Illuminate\Support\Facades\Route;
use Modules\JobsAndObituary\Controllers\JobsAndObituaryController;

Route::get('/', [JobsAndObituaryController::class, 'index'])->name('index');
Route::post('/jobs', [JobsAndObituaryController::class, 'storeJob'])->name('jobs.store');
Route::delete('/jobs/{job}', [JobsAndObituaryController::class, 'destroyJob'])->name('jobs.destroy');
Route::post('/obituaries', [JobsAndObituaryController::class, 'storeObituary'])->name('obituaries.store');
Route::delete('/obituaries/{obituary}', [JobsAndObituaryController::class, 'destroyObituary'])->name('obituaries.destroy');
