<?php

use Illuminate\Support\Facades\Route;
use Modules\ReporterBilling\Controllers\BillingController;

Route::get('/', [BillingController::class, 'index'])->name('index');
Route::post('/calculate', [BillingController::class, 'calculate'])->name('calculate');
Route::post('/', [BillingController::class, 'store'])->name('store');
Route::patch('/{bill}/status', [BillingController::class, 'updateStatus'])->name('update-status');
Route::delete('/{bill}', [BillingController::class, 'destroy'])->name('destroy');
