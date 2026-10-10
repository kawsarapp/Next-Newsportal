<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ModuleManager;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ModuleController extends Controller
{
    public function __construct(
        protected ModuleManager $moduleManager
    ) {}

    public function index(): Response
    {
        $portalId = auth()->user()->portal_id ?? 1;
        $modules = $this->moduleManager->getModulesForPortal((int) $portalId);

        return Inertia::render('Dashboard/Modules/Index', [
            'modules' => $modules,
        ]);
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'module_zip' => 'required|file|mimes:zip|max:20480', // max 20MB
        ]);

        try {
            $zipPath = $request->file('module_zip')->getRealPath();
            $manifest = $this->moduleManager->installFromZip($zipPath);

            return back()->with('success', "মডিউল '{$manifest['name']}' সফলভাবে ইনস্টল করা হয়েছে!");
        } catch (\Throwable $e) {
            return back()->with('error', 'মডিউল ইনস্টলেশনে ত্রুটি: ' . $e->getMessage());
        }
    }

    public function toggle(Request $request, string $slug): RedirectResponse
    {
        $portalId = auth()->user()->portal_id ?? 1;
        $enabled = $request->boolean('enabled');

        $this->moduleManager->toggleModule((int) $portalId, $slug, $enabled);

        return back()->with('success', 'মডিউল স্ট্যাটাস আপডেট হয়েছে!');
    }

    public function destroy(string $slug): RedirectResponse
    {
        try {
            $this->moduleManager->deleteModule($slug);
            return back()->with('success', 'মডিউলটি সম্পূর্ণ মুছে ফেলা হয়েছে!');
        } catch (\Throwable $e) {
            return back()->with('error', 'মডিউল মুছতে ব্যর্থ: ' . $e->getMessage());
        }
    }
}
