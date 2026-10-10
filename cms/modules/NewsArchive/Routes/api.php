<?php

use Illuminate\Support\Facades\Route;
use Modules\NewsArchive\Controllers\ArchiveController;

Route::get('/', [ArchiveController::class, 'searchApi']);
Route::get('/calendar-stats', [ArchiveController::class, 'calendarStats']);
