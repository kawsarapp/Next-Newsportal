<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'billing_cycle' => date('F Y'),
        'total_reporters' => 12,
        'rates' => [
            'lead_story' => 500,
            'regular_story' => 200,
            'photo_story' => 150,
        ],
        'pending_payout' => 38500,
        'paid_this_month' => 64000,
    ]);
});
