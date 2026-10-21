<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PortalSetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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

    public function storeRedirect(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source_url'  => 'required|string|max:255',
            'target_url'  => 'required|string|max:255',
            'status_code' => 'nullable|integer|in:301,302,307,308',
        ]);

        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $setting = PortalSetting::where('portal_id', $portalId)->where('key', 'seo_redirects')->first();
        $redirects = $setting ? json_decode($setting->value, true) : [];
        $redirects[] = array_merge($validated, [
            'id'         => uniqid(),
            'created_at' => now()->toDateTimeString(),
        ]);

        PortalSetting::updateOrCreate(
            ['portal_id' => $portalId, 'key' => 'seo_redirects'],
            [
                'value' => json_encode($redirects),
                'type'  => 'json',
                'group' => 'seo',
            ]
        );

        return back()->with('success', 'রিডাইরেক্ট রুল সফলভাবে সংরক্ষিত হয়েছে!');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        PortalSetting::updateOrCreate(
            ['portal_id' => $portalId, 'key' => 'seo_settings'],
            [
                'value' => json_encode($request->all()),
                'type'  => 'json',
                'group' => 'seo',
            ]
        );

        return back()->with('success', 'এসইও সেটিংস সংরক্ষিত হয়েছে!');
    }
}
