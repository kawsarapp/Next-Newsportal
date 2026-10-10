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
    public function index(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->where('show_in_nav', true)
            ->orderBy('order')
            ->get(['id', 'name', 'name_en', 'slug', 'color', 'order']);

        return $this->success($categories);
    }

    public function show(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$category) {
            return $this->notFound('Category not found');
        }

        return $this->success($category);
    }

    public function tree(): JsonResponse
    {
        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('order')])
            ->orderBy('order')
            ->get();

        return $this->success($categories);
    }

    public function posts(string $slug, Request $request): JsonResponse
    {
        $category = Category::where('slug', $slug)->first();

        if (!$category) {
            return $this->notFound('Category not found');
        }

        $posts = Post::published()
            ->where('category_id', $category->id)
            ->with(['author:id,name', 'category:id,name,slug'])
            ->latest('published_at')
            ->paginate((int) $request->get('per_page', 12));

        return $this->success($posts);
    }
}
