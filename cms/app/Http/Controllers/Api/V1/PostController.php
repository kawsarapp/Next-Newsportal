<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostCollection;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(private readonly PostService $postService) {}

    /**
     * GET /api/v1/posts
     * List published posts with filters
     */
    public function index(Request $request): PostCollection
    {
        $posts = $this->postService->getPublishedForApi(
            filters: $request->only([
                'category', 'category_id', 'tag', 'type', 'author',
                'division_id', 'district_id', 'division', 'district', 'search'
            ]),
            perPage: min($request->integer('per_page', 15), 50),
        );

        return new PostCollection($posts);
    }

    /**
     * GET /api/v1/posts/breaking
     * Breaking news ticker data
     */
    public function breaking(Request $request): JsonResponse
    {
        $breaking = $this->postService->getBreaking(
            limit: min($request->integer('limit', 20), 30)
        );

        return $this->success($breaking->map(fn ($p) => [
            'id'            => $p->id,
            'title'         => $p->title,
            'slug'          => $p->slug,
            'published_at'  => $p->published_at,
            'is_breaking'   => (bool) $p->is_breaking,
            'category_name' => $p->category?->name,
        ]));
    }

    /**
     * GET /api/v1/posts/featured
     * Featured/lead news for homepage
     */
    public function featured(Request $request): JsonResponse
    {
        $featured = $this->postService->getFeatured(
            limit: min($request->integer('limit', 10), 20)
        );

        return $this->success(PostResource::collection($featured));
    }

    /**
     * GET /api/v1/posts/{slug}
     * Single post detail
     */
    public function show(string $slug): JsonResponse
    {
        $decoded = urldecode($slug);
        $post = $this->postService->findBySlug($slug) ?? $this->postService->findBySlug($decoded);

        if (! $post) {
            return $this->notFound('Post not found.');
        }

        return $this->success(new PostResource($post))
            ->header('Cache-Control', 'public, max-age=60, s-maxage=300, stale-while-revalidate=600');
    }

    /**
     * POST /api/v1/posts/{id}/view
     * Record a page view (fire-and-forget)
     */
    public function recordView(int $id, Request $request): JsonResponse
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $referer = $request->header('Referer');
        $deviceType = $this->detectDevice($userAgent);

        dispatch(static function () use ($id, $ip, $userAgent, $referer, $deviceType) {
            $post = \App\Models\Post::find($id);
            if ($post) {
                $post->incrementViewCount();
                \App\Models\PostViewLog::create([
                    'post_id'     => $post->id,
                    'portal_id'   => $post->portal_id,
                    'ip_address'  => $ip,
                    'user_agent'  => $userAgent,
                    'referer'     => $referer,
                    'device_type' => $deviceType,
                    'viewed_at'   => now(),
                ]);
            }
        })->afterResponse();

        return $this->success(['recorded' => true]);
    }

    private function detectDevice(?string $ua): string
    {
        if (! $ua) return 'unknown';
        if (preg_match('/mobile/i', $ua)) return 'mobile';
        if (preg_match('/tablet|ipad/i', $ua)) return 'tablet';
        return 'desktop';
    }
}
