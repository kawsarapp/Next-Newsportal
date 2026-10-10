<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class SocialController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard/Social/Index', [
            'accounts' => [
                ['id' => 1, 'platform' => 'facebook', 'account_name' => 'মূল ফেসবুক পেজ'],
                ['id' => 2, 'platform' => 'telegram', 'account_name' => 'অফিসিয়াল টেলিগ্রাম চ্যানেল'],
                ['id' => 3, 'platform' => 'x', 'account_name' => 'নিউজপোর্টাল এক্স হ্যান্ডল'],
            ]
        ]);
    }

    public function accounts(): Response
    {
        return $this->index();
    }

    public function queue(): Response
    {
        return $this->index();
    }

    public function publish()
    {
        return back()->with('success', 'সোশ্যাল মিডিয়ায় সফলভাবে পোস্ট করা হয়েছে!');
    }
}
