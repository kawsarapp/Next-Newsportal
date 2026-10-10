<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $portalId = auth()->user()->getEffectivePortalId();

        $categories = Category::where('portal_id', $portalId)
            ->orderBy('order')
            ->get();

        return Inertia::render('Dashboard/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $portalId = auth()->user()->getEffectivePortalId();


        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'slug' => 'nullable|string|max:120',
            'order' => 'integer',
            'color' => 'nullable|string|max:20',
            'show_in_nav' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['portal_id'] = $portalId;
        $validated['slug'] = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name_en'] ?: $validated['name']);

        Category::create($validated);

        return back()->with('success', 'ক্যাটাগরি সফলভাবে তৈরি হয়েছে!');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'slug' => 'nullable|string|max:120',
            'order' => 'integer',
            'color' => 'nullable|string|max:20',
            'show_in_nav' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $category->update($validated);

        return back()->with('success', 'ক্যাটাগরি আপডেট করা হয়েছে!');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return back()->with('success', 'ক্যাটাগরি মুছে ফেলা হয়েছে!');
    }
}
