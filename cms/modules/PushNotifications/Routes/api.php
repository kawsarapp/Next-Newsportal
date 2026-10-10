<?php

use Illuminate\Support\Facades\Route;
use Modules\PushNotifications\Controllers\PushController;

Route::post('/subscribe', [PushController::class, 'subscribe']);
Route::get('/stats', function () {
    return response()->json([
        'active_subscribers' => \App\Models\PushSubscription::where('is_active', true)->count(),
        'total_campaigns' => \App\Models\PushCampaign::count(),
    ]);
});
