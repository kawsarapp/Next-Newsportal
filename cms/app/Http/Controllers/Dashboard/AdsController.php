<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AdsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard/Ads/Index', [
            'slots' => [
                ['id' => 1, 'name' => 'হেডার ব্যানার অ্যাড', 'location' => 'header_top'],
                ['id' => 2, 'name' => 'নিউজ বিস্তারিত সাইডবার', 'location' => 'article_sidebar'],
                ['id' => 3, 'name' => 'নিউজ প্যারাগ্রাফের মাঝে', 'location' => 'in_content_middle'],
                ['id' => 4, 'name' => 'হোমপেজ ব্রেকিং এর নিচে', 'location' => 'home_below_ticker'],
            ]
        ]);
    }
}
