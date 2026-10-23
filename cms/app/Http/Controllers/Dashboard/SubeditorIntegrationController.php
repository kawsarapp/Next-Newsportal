<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PortalSetting;
use App\Models\SubeditorLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubeditorIntegrationController extends Controller
{
    private const PORTAL_ID = 1;

    public function index(): Response
    {
        $portalId = self::PORTAL_ID;

        // Fetch settings from portal_settings for portal_id = 1
        $settingsRecords = PortalSetting::where('portal_id', $portalId)
            ->whereIn('key', [
                'subeditor_api_secret',
                'subeditor_auto_publish',
                'subeditor_default_author_id',
                'subeditor_default_category_id',
                'subeditor_enabled',
                'subeditor_frontend_url',
                'frontend_url',
            ])
            ->pluck('value', 'key');

        // Check if api secret exists; if empty, generate secure random token
        $apiSecret = $settingsRecords['subeditor_api_secret'] ?? null;
        if (empty($apiSecret)) {
            $apiSecret = 'sub_sec_' . bin2hex(random_bytes(16));
            PortalSetting::updateOrCreate(
                ['portal_id' => $portalId, 'key' => 'subeditor_api_secret'],
                ['value' => $apiSecret, 'type' => 'string', 'group' => 'subeditor']
            );
        }

        $autoPublish = isset($settingsRecords['subeditor_auto_publish'])
            ? filter_var($settingsRecords['subeditor_auto_publish'], FILTER_VALIDATE_BOOLEAN)
            : true;

        $defaultAuthorId = isset($settingsRecords['subeditor_default_author_id'])
            ? (int) $settingsRecords['subeditor_default_author_id']
            : 1;

        $defaultCategoryId = isset($settingsRecords['subeditor_default_category_id'])
            ? (int) $settingsRecords['subeditor_default_category_id']
            : 1;

        $enabled = isset($settingsRecords['subeditor_enabled'])
            ? filter_var($settingsRecords['subeditor_enabled'], FILTER_VALIDATE_BOOLEAN)
            : true;

        $frontendUrl = $settingsRecords['subeditor_frontend_url']
            ?? $settingsRecords['frontend_url']
            ?? '';

        $settings = [
            'subeditor_api_secret' => $apiSecret,
            'subeditor_auto_publish' => $autoPublish,
            'subeditor_default_author_id' => $defaultAuthorId,
            'subeditor_default_category_id' => $defaultCategoryId,
            'subeditor_enabled' => $enabled,
            'subeditor_frontend_url' => $frontendUrl,
        ];

        // Fetch authors
        $authors = User::select('id', 'name', 'email')->get();

        // Fetch active categories for portal_id = 1
        $categories = Category::where('is_active', true)
            ->where('portal_id', $portalId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Fetch recent incoming logs
        $logs = SubeditorLog::where('portal_id', $portalId)
            ->with('post:id,title,slug')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $endpoints = [
            'webhook_url' => url('/api/v1/subeditor/news'),
            'webhook_url_alias' => url('/api/external-news-post'),
            'category_url' => url('/api/v1/subeditor/categories'),
            'category_url_alias' => url('/api/get-categories'),
        ];

        return Inertia::render('Dashboard/Subeditor/Index', [
            'settings' => $settings,
            'authors' => $authors,
            'categories' => $categories,
            'logs' => $logs,
            'endpoints' => $endpoints,
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $portalId = self::PORTAL_ID;

        $validated = $request->validate([
            'subeditor_api_secret' => 'nullable|string|max:255',
            'subeditor_auto_publish' => 'nullable|boolean',
            'subeditor_default_author_id' => 'required|integer|exists:users,id',
            'subeditor_default_category_id' => 'required|integer|exists:categories,id',
            'subeditor_enabled' => 'nullable|boolean',
            'subeditor_frontend_url' => 'nullable|url|max:255',
        ]);

        $apiSecret = !empty($validated['subeditor_api_secret'])
            ? trim($validated['subeditor_api_secret'])
            : ('sub_sec_' . bin2hex(random_bytes(16)));

        PortalSetting::updateOrCreate(
            ['portal_id' => $portalId, 'key' => 'subeditor_api_secret'],
            ['value' => $apiSecret, 'type' => 'string', 'group' => 'subeditor']
        );

        PortalSetting::updateOrCreate(
            ['portal_id' => $portalId, 'key' => 'subeditor_auto_publish'],
            ['value' => $request->boolean('subeditor_auto_publish') ? '1' : '0', 'type' => 'boolean', 'group' => 'subeditor']
        );

        PortalSetting::updateOrCreate(
            ['portal_id' => $portalId, 'key' => 'subeditor_default_author_id'],
            ['value' => (string) $validated['subeditor_default_author_id'], 'type' => 'integer', 'group' => 'subeditor']
        );

        PortalSetting::updateOrCreate(
            ['portal_id' => $portalId, 'key' => 'subeditor_default_category_id'],
            ['value' => (string) $validated['subeditor_default_category_id'], 'type' => 'integer', 'group' => 'subeditor']
        );

        PortalSetting::updateOrCreate(
            ['portal_id' => $portalId, 'key' => 'subeditor_enabled'],
            ['value' => $request->boolean('subeditor_enabled') ? '1' : '0', 'type' => 'boolean', 'group' => 'subeditor']
        );

        if (!empty($validated['subeditor_frontend_url'])) {
            $frontendUrl = rtrim(trim($validated['subeditor_frontend_url']), '/');
            PortalSetting::updateOrCreate(
                ['portal_id' => $portalId, 'key' => 'frontend_url'],
                ['value' => $frontendUrl, 'type' => 'string', 'group' => 'general']
            );
            PortalSetting::updateOrCreate(
                ['portal_id' => $portalId, 'key' => 'subeditor_frontend_url'],
                ['value' => $frontendUrl, 'type' => 'string', 'group' => 'subeditor']
            );
        } else {
            PortalSetting::where('portal_id', $portalId)
                ->whereIn('key', ['frontend_url', 'subeditor_frontend_url'])
                ->delete();
        }

        return back()->with('success', 'Subeditor24 সেটিংস সফলভাবে সংরক্ষিত হয়েছে।');
    }

    public function generateToken(): RedirectResponse
    {
        $portalId = self::PORTAL_ID;

        $newToken = 'sub_sec_' . bin2hex(random_bytes(16));

        PortalSetting::updateOrCreate(
            ['portal_id' => $portalId, 'key' => 'subeditor_api_secret'],
            ['value' => $newToken, 'type' => 'string', 'group' => 'subeditor']
        );

        return back()->with('success', 'নতুন API সিক্রেট টোকেন সফলভাবে তৈরি হয়েছে।');
    }

    public function clearLogs(): RedirectResponse
    {
        $portalId = self::PORTAL_ID;

        SubeditorLog::where('portal_id', $portalId)->delete();

        return back()->with('success', 'সমস্ত অ্যাক্টিভিটি লগ সফলভাবে মুছে ফেলা হয়েছে।');
    }
}
