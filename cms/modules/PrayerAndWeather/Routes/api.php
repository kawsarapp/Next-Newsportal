<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'district' => 'ঢাকা',
        'date' => date('d M Y'),
        'prayer_times' => [
            'fajr' => '০৪:৩৫ AM',
            'sunrise' => '০৫:৫২ AM',
            'dhuhr' => '১২:০২ PM',
            'asr' => '০৪:১৮ PM',
            'maghrib' => '০৬:০৮ PM',
            'isha' => '০৭:২৫ PM',
        ],
        'weather' => [
            'temp_c' => 28,
            'condition' => 'আংশিক মেঘলা',
            'humidity' => '৬৫%',
        ]
    ]);
});
