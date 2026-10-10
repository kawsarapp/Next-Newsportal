<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'Polls & Surveys module API is working perfectly!',
    ]);
});
