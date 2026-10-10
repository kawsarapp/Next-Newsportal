<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'edition_date' => date('Y-m-d'),
        'total_pages' => 8,
        'pages' => [
            ['page_no' => 1, 'title' => 'প্রথম পাতা', 'image' => '/storage/epaper/page_1.webp'],
            ['page_no' => 2, 'title' => 'জাতীয় ও রাজনীতি', 'image' => '/storage/epaper/page_2.webp'],
            ['page_no' => 3, 'title' => 'আন্তর্জাতিক সংবাদ', 'image' => '/storage/epaper/page_3.webp'],
            ['page_no' => 4, 'title' => 'খেলাধুলা', 'image' => '/storage/epaper/page_4.webp'],
        ]
    ]);
});
