<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'match' => 'বাংলাদেশ বনাম ভারত (টি-টোয়েন্টি সিরিজ)',
        'score' => [
            'team_a' => ['name' => 'বাংলাদেশ', 'runs' => 178, 'wickets' => 4, 'overs' => '20.0'],
            'team_b' => ['name' => 'ভারত', 'runs' => 142, 'wickets' => 7, 'overs' => '17.3'],
        ],
        'status' => 'ভারতের প্রয়োজন ১৫ বলে ৩৭ রান',
        'current_batter' => 'হার্দিক পান্ডিয়া (৩২*)',
        'current_bowler' => 'মুস্তাফিজুর রহমান (৩/২২)',
    ]);
});
