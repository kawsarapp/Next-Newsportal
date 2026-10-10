<?php

use Illuminate\Support\Facades\Route;
use Modules\PushNotifications\Controllers\PushController;

Route::get('/', [PushController::class, 'index'])->name('index');
Route::post('/send', [PushController::class, 'send'])->name('send');
Route::delete('/{campaign}', [PushController::class, 'destroy'])->name('destroy');
