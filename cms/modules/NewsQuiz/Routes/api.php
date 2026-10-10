<?php

use Illuminate\Support\Facades\Route;
use Modules\NewsQuiz\Controllers\QuizController;

Route::get('/', [QuizController::class, 'getPublicQuizzes']);
Route::get('/{slug}', [QuizController::class, 'getQuizDetail']);
Route::post('/{quiz}/submit', [QuizController::class, 'submitQuiz']);
