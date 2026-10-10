<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'event_title' => 'জাতীয় বাজেট ২০২৬-২৭: লাইভ আপডেট',
        'status' => 'live',
        'updates' => [
            [
                'time' => '০৩:১৫ PM',
                'headline' => 'প্রযুক্তি খাতে কর অব্যাহতির প্রস্তাব',
                'body' => 'দেশীয় সফটওয়্যার ও হার্ডওয়্যার প্রস্তুতকারক প্রতিষ্ঠানের জন্য বিশেষ প্রণোদনা ঘোষণা করা হয়েছে।',
            ],
            [
                'time' => '০২:৪৫ PM',
                'headline' => 'বাজেট অধিবেশন শুরু',
                'body' => 'সংসদে বাজেট বক্তৃতা শুরু করেছেন অর্থমন্ত্রী।',
            ]
        ]
    ]);
});
