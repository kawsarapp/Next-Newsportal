<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard/Analytics/Index');
    }

    public function realtime(): Response
    {
        return $this->index();
    }

    public function reports(): Response
    {
        return $this->index();
    }

    public function export()
    {
        return back()->with('success', 'রিপোর্ট ডাউনলোড সফল হয়েছে!');
    }
}
