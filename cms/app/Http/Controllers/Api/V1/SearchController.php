<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            return $this->success([]);
        }

        $posts = Post::published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%");
            })
            ->with(['author:id,name', 'category:id,name,slug'])
            ->latest('published_at')
            ->limit(20)
            ->get();

        return $this->success($posts);
    }
}
