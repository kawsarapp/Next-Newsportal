<?php

use Illuminate\Support\Facades\Route;
use Modules\NewsArchive\Controllers\ArchiveController;

Route::get('/', [ArchiveController::class, 'index'])->name('index');
