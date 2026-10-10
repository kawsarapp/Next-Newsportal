<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\Category;
use App\Models\PortalSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class FrontendController extends Controller
{
    public function index(): Response
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $sections = HomepageSection::where('portal_id', $portalId)
            ->with(['category:id,name,slug'])
            ->orderBy('order')
            ->get();

        $categories = Category::where('portal_id', $portalId)
            ->where('is_active', true)
            ->get(['id', 'name', 'slug']);

        $sidebarSetting = PortalSetting::where('portal_id', $portalId)
            ->where('key', 'sidebar_config')
            ->first();

        $sidebarConfig = $sidebarSetting ? json_decode($sidebarSetting->value, true) : [
            'show_tabs'        => true,
            'tabs_title_1'     => 'সর্বশেষ সংবাদ',
            'tabs_title_2'     => 'সর্বাধিক পঠিত',
            'tabs_count'       => 5,
            'show_related'     => true,
            'related_title'    => 'একই বিভাগের আরও খবর',
            'related_count'    => 4,
            'show_ad'          => true,
            'ad_title'         => 'বিজ্ঞাপন স্থান (Sidebar Ad)',
            'ad_text'          => 'আপনার ব্র্যান্ড বা পণ্যের বিজ্ঞাপন দিন',
            'ad_contact'       => 'যোগাযোগ: ads@newsportal.com',
            'ad_link'          => '',
            'ad_image'         => '',
            'show_newsletter'  => true,
            'newsletter_title' => 'দৈনিক বাংলা ই-নিউজলেটার',
            'newsletter_desc'  => 'প্রতিদিনের সেরা ও বিশ্লেষণমূলক সংবাদের আপডেট পেতে সাবস্ক্রাইব করুন।',
        ];

        $headerFooterSetting = PortalSetting::where('portal_id', $portalId)
            ->where('key', 'header_footer_config')
            ->first();

        $headerFooterConfig = $headerFooterSetting ? json_decode($headerFooterSetting->value, true) : [
            // Header Settings
            'show_topbar'      => true,
            'topbar_location'  => 'ঢাকা, বাংলাদেশ',
            'prayer_time'      => '🕌 মাগরিব ০৬:০৮ PM',
            'weather_text'     => '🌦️ ২৮° সে.',
            'site_tagline'     => 'সত্য ও ন্যায়ের সাথে সার্বক্ষণিক',
            'show_epaper_btn'  => true,
            'epaper_url'       => '/epaper',
            'show_live_btn'    => true,
            'live_url'         => '/live',
            // Footer Settings
            'about_bio'        => 'সত্য ও বস্তুনিষ্ঠ সাংবাদিকতার অঙ্গীকার নিয়ে সর্বাধুনিক হেডলেস প্রযুক্তিতে পরিচালিত ডিজিটাল নিউজ মিডিয়া।',
            'office_address'   => 'প্রধান কার্যালয়: ঢাকা, বাংলাদেশ',
            'contact_email'    => 'newsroom@dainikbangla.com',
            'contact_phone'    => '+৮৮০১৭০০০০০০০০',
            'facebook_url'     => 'https://facebook.com',
            'twitter_url'      => 'https://twitter.com',
            'youtube_url'      => 'https://youtube.com',
            'copyright_text'   => '© ২০২৬ দৈনিক বাংলা নিউজপোর্টাল। সর্বস্বত্ব সংরক্ষিত।',
        ];

        $youtubeSetting = PortalSetting::where('portal_id', $portalId)
            ->where('key', 'youtube_config')
            ->first();

        $youtubeConfig = $youtubeSetting ? json_decode($youtubeSetting->value, true) : [
            'is_live'          => false,
            'live_video_url'   => '',
            'live_title'       => '🔴 সরাসরি সম্প্রচার: বিশেষ সংবাদ ও বিশ্লেষণ',
            'channel_url'      => 'https://youtube.com',
            'show_on_home'     => true,
            'home_position'    => 'middle',
            'show_on_live_page'=> true,
            'videos'           => [
                [
                    'title'     => 'আজকের প্রধান সংবাদ ও শীর্ষ শিরোনামসমূহ',
                    'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'duration'  => '১০:১৫',
                ],
                [
                    'title'     => 'অর্থনীতি ও বাজেট নিয়ে বিশেষজ্ঞদের খোলামেলা আলোচনা',
                    'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'duration'  => '১৫:২০',
                ],
                [
                    'title'     => 'খেলাধুলার মাঠের সর্বশেষ খবরাখবর ও রোমাঞ্চকর মুহূর্ত',
                    'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'duration'  => '০৮:৪৫',
                ],
                [
                    'title'     => 'প্রযুক্তি দুনিয়ার নিত্যনতুন উদ্ভাবন ও ভবিষ্যৎ ভাবনা',
                    'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'duration'  => '১২:৩০',
                ],
            ],
        ];

        return Inertia::render('Dashboard/Frontend/Index', [
            'sections'           => $sections,
            'categories'         => $categories,
            'sidebarConfig'      => $sidebarConfig,
            'headerFooterConfig' => $headerFooterConfig,
            'youtubeConfig'      => $youtubeConfig,
        ]);
    }

    public function homepage(): Response
    {
        return $this->index();
    }

    public function storeSection(Request $request): RedirectResponse
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $validated = $request->validate([
            'title'        => 'required|string|max:150',
            'section_type' => 'required|string|in:hero,grid,featured,ad,list,slider',
            'layout_type'  => 'required|string|in:grid,list,split,banner,slider',
            'category_id'  => 'nullable|exists:categories,id',
            'posts_count'  => 'required|integer|min:1|max:24',
            'is_active'    => 'boolean',
            'config'       => 'nullable|array',
        ]);

        $maxOrder = HomepageSection::where('portal_id', $portalId)->max('order') ?? 0;

        HomepageSection::create([
            'portal_id'    => $portalId,
            'title'        => $validated['title'],
            'section_type' => $validated['section_type'],
            'layout_type'  => $validated['layout_type'],
            'category_id'  => $validated['category_id'] ?: null,
            'posts_count'  => $validated['posts_count'],
            'order'        => $maxOrder + 1,
            'is_active'    => $validated['is_active'] ?? true,
            'config'       => $validated['config'] ?? null,
        ]);

        return back()->with('success', 'নতুন হোমপেজ সেকশন সফলভাবে যুক্ত হয়েছে!');
    }

    public function updateSection(Request $request, HomepageSection $section): RedirectResponse
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:150',
            'section_type' => 'required|string|in:hero,grid,featured,ad,list,slider',
            'layout_type'  => 'required|string|in:grid,list,split,banner,slider',
            'category_id'  => 'nullable|exists:categories,id',
            'posts_count'  => 'required|integer|min:1|max:24',
            'is_active'    => 'boolean',
            'config'       => 'nullable|array',
        ]);

        $section->update([
            'title'        => $validated['title'],
            'section_type' => $validated['section_type'],
            'layout_type'  => $validated['layout_type'],
            'category_id'  => $validated['category_id'] ?: null,
            'posts_count'  => $validated['posts_count'],
            'is_active'    => $validated['is_active'] ?? true,
            'config'       => $validated['config'] ?? $section->config,
        ]);

        return back()->with('success', 'সেকশন আপডেট সম্পন্ন হয়েছে!');
    }

    public function reorderSections(Request $request): RedirectResponse
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $validated = $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|integer|exists:homepage_sections,id',
            'sections.*.order' => 'required|integer',
            'sections.*.is_active' => 'required|boolean',
        ]);

        foreach ($validated['sections'] as $item) {
            HomepageSection::where('id', $item['id'])
                ->where('portal_id', $portalId)
                ->update([
                    'order' => $item['order'],
                    'is_active' => $item['is_active'],
                ]);
        }

        return back()->with('success', 'হোমপেজ সেকশন লেআউট সফলভাবে সংরক্ষিত হয়েছে!');
    }

    public function toggleSection(HomepageSection $section): RedirectResponse
    {
        $section->update(['is_active' => !$section->is_active]);
        return back()->with('success', 'সেকশন স্ট্যাটাস পরিবর্তিত হয়েছে!');
    }

    public function destroySection(HomepageSection $section): RedirectResponse
    {
        $section->delete();
        return back()->with('success', 'হোমপেজ সেকশন মুছে ফেলা হয়েছে।');
    }

    public function updateSidebar(Request $request): RedirectResponse
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $validated = $request->validate([
            'show_tabs'        => 'boolean',
            'tabs_title_1'     => 'nullable|string|max:100',
            'tabs_title_2'     => 'nullable|string|max:100',
            'tabs_count'       => 'integer|min:1|max:15',
            'show_related'     => 'boolean',
            'related_title'    => 'nullable|string|max:100',
            'related_count'    => 'integer|min:1|max:10',
            'show_ad'          => 'boolean',
            'ad_title'         => 'nullable|string|max:100',
            'ad_text'          => 'nullable|string|max:200',
            'ad_contact'       => 'nullable|string|max:150',
            'ad_link'          => 'nullable|string|max:255',
            'ad_image'         => 'nullable|string|max:255',
            'show_newsletter'  => 'boolean',
            'newsletter_title' => 'nullable|string|max:150',
            'newsletter_desc'  => 'nullable|string|max:300',
        ]);

        PortalSetting::updateOrCreate(
            ['portal_id' => $portalId, 'key' => 'sidebar_config'],
            [
                'value' => json_encode($validated),
                'type'  => 'json',
                'group' => 'frontend',
            ]
        );

        return back()->with('success', 'সাইডবার সেটিংস সফলভাবে সংরক্ষিত হয়েছে!');
    }

    public function publicSidebar(): JsonResponse
    {
        $portalId = 1;

        $sidebarSetting = PortalSetting::where('portal_id', $portalId)
            ->where('key', 'sidebar_config')
            ->first();

        $sidebarConfig = $sidebarSetting ? json_decode($sidebarSetting->value, true) : [
            'show_tabs'        => true,
            'tabs_title_1'     => 'সর্বশেষ সংবাদ',
            'tabs_title_2'     => 'সর্বাধিক পঠিত',
            'tabs_count'       => 5,
            'show_related'     => true,
            'related_title'    => 'একই বিভাগের আরও খবর',
            'related_count'    => 4,
            'show_ad'          => true,
            'ad_title'         => 'বিজ্ঞাপন স্থান (Sidebar Ad)',
            'ad_text'          => 'আপনার ব্র্যান্ড বা পণ্যের বিজ্ঞাপন দিন',
            'ad_contact'       => 'যোগাযোগ: ads@newsportal.com',
            'ad_link'          => '',
            'ad_image'         => '',
            'show_newsletter'  => true,
            'newsletter_title' => 'দৈনিক বাংলা ই-নিউজলেটার',
            'newsletter_desc'  => 'প্রতিদিনের সেরা ও বিশ্লেষণমূলক সংবাদের আপডেট পেতে সাবস্ক্রাইব করুন।',
        ];

        return response()->json([
            'success' => true,
            'data'    => $sidebarConfig,
        ]);
    }

    public function updateHeaderFooter(Request $request): RedirectResponse
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $validated = $request->validate([
            'show_topbar'      => 'boolean',
            'topbar_location'  => 'nullable|string|max:100',
            'prayer_time'      => 'nullable|string|max:100',
            'weather_text'     => 'nullable|string|max:100',
            'site_tagline'     => 'nullable|string|max:200',
            'show_epaper_btn'  => 'boolean',
            'epaper_url'       => 'nullable|string|max:200',
            'show_live_btn'    => 'boolean',
            'live_url'         => 'nullable|string|max:200',
            'about_bio'        => 'nullable|string|max:500',
            'office_address'   => 'nullable|string|max:200',
            'contact_email'    => 'nullable|string|max:150',
            'contact_phone'    => 'nullable|string|max:50',
            'facebook_url'     => 'nullable|string|max:255',
            'twitter_url'      => 'nullable|string|max:255',
            'youtube_url'      => 'nullable|string|max:255',
            'copyright_text'   => 'nullable|string|max:200',
        ]);

        PortalSetting::updateOrCreate(
            ['portal_id' => $portalId, 'key' => 'header_footer_config'],
            [
                'value' => json_encode($validated),
                'type'  => 'json',
                'group' => 'frontend',
            ]
        );

        return back()->with('success', 'হেডার ও ফুটার সেটিংস সফলভাবে সংরক্ষিত হয়েছে!');
    }

    public function publicHeaderFooter(): JsonResponse
    {
        $portalId = 1;

        $setting = PortalSetting::where('portal_id', $portalId)
            ->where('key', 'header_footer_config')
            ->first();

        $config = $setting ? json_decode($setting->value, true) : [
            'show_topbar'      => true,
            'topbar_location'  => 'ঢাকা, বাংলাদেশ',
            'prayer_time'      => '🕌 মাগরিব ০৬:০৮ PM',
            'weather_text'     => '🌦️ ২৮° সে.',
            'site_tagline'     => 'সত্য ও ন্যায়ের সাথে সার্বক্ষণিক',
            'show_epaper_btn'  => true,
            'epaper_url'       => '/epaper',
            'show_live_btn'    => true,
            'live_url'         => '/live',
            'about_bio'        => 'সত্য ও বস্তুনিষ্ঠ সাংবাদিকতার অঙ্গীকার নিয়ে সর্বাধুনিক হেডলেস প্রযুক্তিতে পরিচালিত ডিজিটাল নিউজ মিডিয়া।',
            'office_address'   => 'প্রধান কার্যালয়: ঢাকা, বাংলাদেশ',
            'contact_email'    => 'newsroom@dainikbangla.com',
            'contact_phone'    => '+৮৮০১৭০০০০০০০০',
            'facebook_url'     => 'https://facebook.com',
            'twitter_url'      => 'https://twitter.com',
            'youtube_url'      => 'https://youtube.com',
            'copyright_text'   => '© ২০২৬ দৈনিক বাংলা নিউজপোর্টাল। সর্বস্বত্ব সংরক্ষিত।',
        ];

        return response()->json([
            'success' => true,
            'data'    => $config,
        ]);
    }

    public function updateYouTube(Request $request): RedirectResponse
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $validated = $request->validate([
            'is_live'           => 'boolean',
            'live_video_url'    => 'nullable|string|max:255',
            'live_title'        => 'nullable|string|max:200',
            'channel_url'       => 'nullable|string|max:255',
            'show_on_home'      => 'boolean',
            'home_position'     => 'nullable|string|in:top,middle,bottom',
            'show_on_live_page' => 'boolean',
            'videos'            => 'nullable|array',
            'videos.*.title'    => 'nullable|string|max:200',
            'videos.*.video_url'=> 'nullable|string|max:255',
            'videos.*.duration' => 'nullable|string|max:50',
        ]);

        PortalSetting::updateOrCreate(
            ['portal_id' => $portalId, 'key' => 'youtube_config'],
            [
                'value' => json_encode($validated),
                'type'  => 'json',
                'group' => 'frontend',
            ]
        );

        return back()->with('success', 'YouTube লাইভ ও ভিডিও সেটিংস সংরক্ষিত হয়েছে!');
    }

    public function publicYouTube(): JsonResponse
    {
        $portalId = 1;

        $setting = PortalSetting::where('portal_id', $portalId)
            ->where('key', 'youtube_config')
            ->first();

        $config = $setting ? json_decode($setting->value, true) : [
            'is_live'          => false,
            'live_video_url'   => '',
            'live_title'       => '🔴 সরাসরি সম্প্রচার: বিশেষ সংবাদ ও বিশ্লেষণ',
            'channel_url'      => 'https://youtube.com',
            'show_on_home'     => true,
            'home_position'    => 'middle',
            'show_on_live_page'=> true,
            'videos'           => [],
        ];

        return response()->json([
            'success' => true,
            'data'    => $config,
        ]);
    }

    public function publicSections(): JsonResponse
    {
        $portalId = 1;

        $sections = HomepageSection::where('portal_id', $portalId)
            ->where('is_active', true)
            ->with(['category:id,name,slug'])
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $sections,
        ]);
    }
}
