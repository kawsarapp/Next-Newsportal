<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AiController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard/Ai/Index');
    }

    public function headlines()
    {
        return response()->json(['success' => true]);
    }

    public function summary()
    {
        return response()->json(['success' => true]);
    }

    public function socialCaption()
    {
        return response()->json(['success' => true]);
    }

    public function grammarCheck()
    {
        return response()->json(['success' => true]);
    }

    public function translate()
    {
        return response()->json(['success' => true]);
    }

    public function seoSuggestions()
    {
        return response()->json(['success' => true]);
    }

    public function usage(): Response
    {
        return Inertia::render('Dashboard/Ai/Index');
    }

    public function settings(): Response
    {
        return Inertia::render('Dashboard/Ai/Index');
    }

    public function updateSettings()
    {
        return back()->with('success', 'সেটিংস আপডেট হয়েছে');
    }
}
