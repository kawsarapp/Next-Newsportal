<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AudienceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard/Audience/Index');
    }

    public function comments(): Response
    {
        return $this->index();
    }

    public function approveComment($id)
    {
        return back()->with('success', 'মন্তব্য অনুমোদিত হয়েছে!');
    }

    public function spamComment($id)
    {
        return back()->with('success', 'মন্তব্য স্প্যাম হিসেবে চিহ্নিত হয়েছে!');
    }

    public function subscribers(): Response
    {
        return $this->index();
    }

    public function pushNotifications(): Response
    {
        return $this->index();
    }

    public function sendPush()
    {
        return back()->with('success', 'পুশ নোটিফিকেশন পাঠানো হয়েছে!');
    }
}
