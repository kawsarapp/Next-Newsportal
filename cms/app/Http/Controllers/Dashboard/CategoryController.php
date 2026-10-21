<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $portalId = auth()->user()->getEffectivePortalId();

        $categories = Category::where('portal_id', $portalId)
            ->with('parent:id,name,slug')
            ->get();

        $hierarchicalCategories = $this->buildHierarchicalList($categories);

        return Inertia::render('Dashboard/Categories/Index', [
            'categories' => $hierarchicalCategories,
        ]);
    }

    /**
     * Recursively sort categories into a clean hierarchical order:
     * Roots (order 1..N) -> each root's children (order 1..N) -> each child's grandchildren (order 1..N).
     */
    private function buildHierarchicalList($categories, ?int $parentId = null, int $depth = 0): array
    {
        $result = [];
        $children = $categories->where('parent_id', $parentId)->sortBy('order')->values();

        foreach ($children as $child) {
            $child->depth = $depth;
            $result[] = $child;
            $subChildren = $this->buildHierarchicalList($categories, (int) $child->id, $depth + 1);
            foreach ($subChildren as $subChild) {
                $result[] = $subChild;
            }
        }

        return $result;
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('dashboard.categories.index');
    }

    public function edit(Category $category): RedirectResponse
    {
        return redirect()->route('dashboard.categories.index', ['edit' => $category->id]);
    }

    public function reorder(Request $request): RedirectResponse
    {
        $items = $request->input('items', $request->input('orders', []));

        if (!empty($items) && is_array($items)) {
            foreach ($items as $index => $item) {
                if (is_array($item) && isset($item['id'])) {
                    Category::where('id', $item['id'])->update(['order' => $item['order'] ?? $index]);
                } elseif (is_numeric($item)) {
                    Category::where('id', $item)->update(['order' => $index]);
                }
            }
        }

        return back()->with('success', 'ক্যাটাগরির ক্রম সফলভাবে আপডেট করা হয়েছে!');
    }

    /**
     * Generate a robust unique slug within the portal.
     * Prevents duplicate entry or empty slug issues when pure Bengali text is used.
     */
    private function generateUniqueSlug(string $name, ?string $nameEn, ?string $customSlug, int $portalId, ?int $ignoreId = null): string
    {
        $slug = !empty($customSlug)
            ? Str::slug($customSlug)
            : (!empty($nameEn) ? Str::slug($nameEn) : Str::slug($name));

        // If Str::slug resulted in empty string (e.g. pure Bengali text without transliteration)
        if (empty($slug)) {
            // Try to create a phonetic transliteration or clean fallback
            $hash = substr(md5($name . microtime()), 0, 6);
            $slug = 'cat-' . $hash;
        }

        $baseSlug = $slug;
        $counter = 1;
        while (
            Category::withoutGlobalScopes()
                ->where('portal_id', $portalId)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $portalId = auth()->user()->getEffectivePortalId();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'slug' => 'nullable|string|max:120',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'order' => 'nullable|integer',
            'color' => 'nullable|string|max:20',
            'show_in_nav' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $validated['portal_id'] = $portalId;
            $validated['show_in_nav'] = $request->boolean('show_in_nav', true);
            $validated['is_active'] = $request->boolean('is_active', true);

            // Default color
            if (empty($validated['color'])) {
                $validated['color'] = '#e63946';
            }

            // Default order
            if (!isset($validated['order']) || $validated['order'] === '' || $validated['order'] === null) {
                $maxOrder = Category::withoutGlobalScopes()->where('portal_id', $portalId)->max('order') ?? 0;
                $validated['order'] = $maxOrder + 1;
            } else {
                $validated['order'] = (int) $validated['order'];
            }

            // Generate unique slug
            $validated['slug'] = $this->generateUniqueSlug(
                name: $validated['name'],
                nameEn: $validated['name_en'] ?? null,
                customSlug: $validated['slug'] ?? null,
                portalId: $portalId
            );

            if (!empty($validated['parent_id'])) {
                $parent = Category::withoutGlobalScopes()->where('portal_id', $portalId)->find($validated['parent_id']);
                if (!$parent) {
                    throw ValidationException::withMessages(['parent_id' => 'নির্বাচিত প্যারেন্ট ক্যাটাগরি পাওয়া যায়নি।']);
                }
                $validated['depth'] = $parent->depth + 1;
            } else {
                $validated['depth'] = 0;
                $validated['parent_id'] = null;
            }

            $category = Category::create($validated);

            if ($request->header('X-Inertia')) {
                return back()->with('success', 'ক্যাটাগরি সফলভাবে তৈরি হয়েছে!');
            }

            if ($request->expectsJson() && !$request->header('X-Inertia')) {
                return response()->json([
                    'success' => true,
                    'category' => $category,
                    'message' => 'ক্যাটাগরি সফলভাবে তৈরি হয়েছে!',
                ]);
            }

            return back()->with('success', 'ক্যাটাগরি সফলভাবে তৈরি হয়েছে!');
        } catch (ValidationException $ve) {
            throw $ve;
        } catch (\Throwable $e) {
            Log::error('Category creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->header('X-Inertia')) {
                return back()->withErrors(['error' => 'ক্যাটাগরি তৈরিতে ত্রুটি হয়েছে: ' . $e->getMessage()])->withInput();
            }

            if ($request->expectsJson() && !$request->header('X-Inertia')) {
                return response()->json([
                    'success' => false,
                    'message' => 'ক্যাটাগরি তৈরিতে ত্রুটি হয়েছে: ' . $e->getMessage(),
                ], 422);
            }

            return back()->withErrors(['error' => 'ক্যাটাগরি তৈরিতে ত্রুটি হয়েছে: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, Category $category): RedirectResponse|JsonResponse
    {
        $portalId = auth()->user()->getEffectivePortalId();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'slug' => 'nullable|string|max:120',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'order' => 'nullable|integer',
            'color' => 'nullable|string|max:20',
            'show_in_nav' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $validated['portal_id'] = $portalId;
            $validated['show_in_nav'] = $request->boolean('show_in_nav', true);
            $validated['is_active'] = $request->boolean('is_active', true);

            if (empty($validated['color'])) {
                $validated['color'] = '#e63946';
            }
            if (isset($validated['order'])) {
                $validated['order'] = (int) $validated['order'];
            }

            // Generate unique slug ignoring current category ID
            $validated['slug'] = $this->generateUniqueSlug(
                name: $validated['name'],
                nameEn: $validated['name_en'] ?? null,
                customSlug: $validated['slug'] ?? null,
                portalId: $portalId,
                ignoreId: $category->id
            );

            // Prevent setting self as parent
            if (isset($validated['parent_id']) && (int) $validated['parent_id'] === $category->id) {
                $validated['parent_id'] = null;
            }

            if (!empty($validated['parent_id'])) {
                $parent = Category::withoutGlobalScopes()->where('portal_id', $portalId)->find($validated['parent_id']);
                if (!$parent) {
                    throw ValidationException::withMessages(['parent_id' => 'নির্বাচিত প্যারেন্ট ক্যাটাগরি পাওয়া যায়নি।']);
                }
                $validated['depth'] = $parent->depth + 1;
            } else {
                $validated['depth'] = 0;
                $validated['parent_id'] = null;
            }

            $category->update($validated);

            if ($request->header('X-Inertia')) {
                return back()->with('success', 'ক্যাটাগরি আপডেট করা হয়েছে!');
            }

            if ($request->expectsJson() && !$request->header('X-Inertia')) {
                return response()->json([
                    'success' => true,
                    'category' => $category,
                    'message' => 'ক্যাটাগরি আপডেট করা হয়েছে!',
                ]);
            }

            return back()->with('success', 'ক্যাটাগরি আপডেট করা হয়েছে!');
        } catch (ValidationException $ve) {
            throw $ve;
        } catch (\Throwable $e) {
            Log::error('Category update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->header('X-Inertia')) {
                return back()->withErrors(['error' => 'ক্যাটাগরি আপডেটে ত্রুটি হয়েছে: ' . $e->getMessage()])->withInput();
            }

            if ($request->expectsJson() && !$request->header('X-Inertia')) {
                return response()->json([
                    'success' => false,
                    'message' => 'ক্যাটাগরি আপডেটে ত্রুটি হয়েছে: ' . $e->getMessage(),
                ], 422);
            }

            return back()->withErrors(['error' => 'ক্যাটাগরি আপডেটে ত্রুটি হয়েছে: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->update(['is_active' => false]);
        $category->delete();
        return back()->with('success', 'ক্যাটাগরি মুছে ফেলা হয়েছে!');
    }
}
