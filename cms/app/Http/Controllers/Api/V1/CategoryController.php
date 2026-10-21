<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Category::whereNull('parent_id')
            ->where('is_active', true);

        if ($request->has('show_in_nav')) {
            $query->where('show_in_nav', $request->boolean('show_in_nav'));
        } else {
            $query->where('show_in_nav', true);
        }

        $categories = $query->with([
            'children' => fn ($q) => $q->where('is_active', true)->orderBy('order')->with([
                'children' => fn ($sub) => $sub->where('is_active', true)->orderBy('order'),
            ]),
        ])
        ->orderBy('order')
        ->get();

        return $this->success($categories);
    }

    private function resolveCategorySlug(string $slug): string
    {
        $aliases = [
            'antarjatik'   => 'international',
            'criket'       => 'cricket',
            'futbol'       => 'football',
            'annanno'      => 'sports-others',
            'ananyo'       => 'others',
            'biswa'        => 'international',
            'world'        => 'international',
            'dhorm'        => 'dhormo',
            'pres-reliz'   => 'press-release',
            'star-special' => 'ster-bisesh',
            'europe'       => 'europ',
        ];

        return $aliases[$slug] ?? $slug;
    }

    public function show(string $slug): JsonResponse
    {
        $targetSlug = $this->resolveCategorySlug($slug);

        $category = Category::where('slug', $targetSlug)
            ->where('is_active', true)
            ->with([
                'parent',
                'children' => fn ($q) => $q->where('is_active', true)->orderBy('order')->with([
                    'children' => fn ($sub) => $sub->where('is_active', true)->orderBy('order'),
                ]),
            ])
            ->first();

        if (!$category) {
            return $this->notFound('Category not found');
        }

        return $this->success($category);
    }

    public function tree(Request $request): JsonResponse
    {
        $query = Category::whereNull('parent_id')
            ->where('is_active', true);

        if ($request->has('show_in_nav')) {
            $query->where('show_in_nav', $request->boolean('show_in_nav'));
        }

        $categories = $query->with([
            'children' => fn ($q) => $q->where('is_active', true)->orderBy('order')->with([
                'children' => fn ($sub) => $sub->where('is_active', true)->orderBy('order'),
            ]),
        ])
        ->orderBy('order')
        ->get();

        return $this->success($categories);
    }

    public function posts(string $slug, Request $request): JsonResponse
    {
        $targetSlug = $this->resolveCategorySlug($slug);

        $category = Category::where('slug', $targetSlug)->first();

        if (!$category) {
            return $this->notFound('Category not found');
        }

        $childIds = $category->children()->pluck('id')->toArray();
        $categoryIds = array_merge([$category->id], $childIds);

        $posts = Post::published()
            ->where(function ($q) use ($categoryIds) {
                $q->whereIn('category_id', $categoryIds)
                  ->orWhereHas('secondaryCategories', fn ($sq) => $sq->whereIn('categories.id', $categoryIds));
                foreach ($categoryIds as $cid) {
                    $q->orWhereJsonContains('additional_category_ids', (int) $cid)
                      ->orWhereJsonContains('additional_category_ids', (string) $cid);
                }
            })
            ->with(['author:id,name,avatar', 'category:id,name,slug', 'secondaryCategories:id,name,slug', 'tags:id,name,slug'])
            ->latest('published_at')
            ->paginate((int) $request->get('per_page', 12));

        return $this->success($posts);
    }
}
