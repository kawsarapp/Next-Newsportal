<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class SeoController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard/Seo/Index');
    }

    public function sitemap(): Response
    {
        return Inertia::render('Dashboard/Seo/Index');
    }

    public function redirects(): Response
    {
        return Inertia::render('Dashboard/Seo/Index');
    }

    public function schema(): Response
    {
        return Inertia::render('Dashboard/Seo/Index');
    }
}
