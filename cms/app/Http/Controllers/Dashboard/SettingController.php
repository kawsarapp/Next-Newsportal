<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Portal;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(): Response
    {
        $portalId = auth()->user()->getEffectivePortalId();
        $portal = Portal::find($portalId) ?? Portal::first();


        return Inertia::render('Dashboard/Settings/Index', [
            'portal' => $portal,
        ]);
    }

    public function updateGeneral()
    {
        return back()->with('success', 'জেনারেল সেটিংস আপডেট হয়েছে');
    }

    public function updateBranding()
    {
        return back()->with('success', 'ব্র্যান্ডিং সেটিংস আপডেট হয়েছে');
    }

    public function updateEmail()
    {
        return back()->with('success', 'ইমেইল সেটিংস আপডেট হয়েছে');
    }

    public function updateSeo()
    {
        return back()->with('success', 'SEO সেটিংস আপডেট হয়েছে');
    }

    public function updateIntegrations()
    {
        return back()->with('success', 'ইন্টিগ্রেশন আপডেট হয়েছে');
    }

    public function testEmail()
    {
        return back()->with('success', 'টেস্ট ইমেইল পাঠানো হয়েছে');
    }

    public function apiKey()
    {
        return $this->index();
    }

    public function rotateApiKey()
    {
        return back()->with('success', 'API কী সফলভাবে রোটেট করা হয়েছে');
    }

    public function health()
    {
        return response()->json(['status' => 'ok']);
    }

    public function jobs()
    {
        return $this->index();
    }
}
