<?php

use Illuminate\Support\Facades\Route;
use Modules\NewsQuiz\Controllers\QuizController;

Route::get('/', [QuizController::class, 'index'])->name('index');
Route::post('/', [QuizController::class, 'store'])->name('store');
Route::patch('/{quiz}/status', [QuizController::class, 'updateStatus'])->name('update-status');
Route::delete('/{quiz}', [QuizController::class, 'destroy'])->name('destroy');
