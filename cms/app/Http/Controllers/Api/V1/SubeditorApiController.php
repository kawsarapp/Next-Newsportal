<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domain\Post\ValueObjects\PostStatus;
use App\Domain\Post\ValueObjects\PostType;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Portal;
use App\Models\PortalSetting;
use App\Models\Post;
use App\Models\SubeditorLog;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SubeditorApiController extends Controller
{
    /**
     * Handle incoming news post from Subeditor24
     */
    public function news(Request $request): JsonResponse
    {
        $portalId = (int) ($request->header('X-Portal-Id') ?: $request->input('portal_id', 1));
        $portal = Portal::withoutGlobalScopes()->find($portalId) ?? Portal::first();
        if ($portal) {
            $portalId = $portal->id;
            app()->instance('current.portal', $portal);
        }

        // 1. Validate Secret Token
        if (!$this->validateToken($request, $portalId)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Normalize possible field aliases from Subeditor24
        if (!$request->has('title') && $request->has('post_title')) {
            $request->merge(['title' => $request->input('post_title')]);
        }
        if (!$request->has('content') && $request->has('post_content')) {
            $request->merge(['content' => $request->input('post_content')]);
        }
        if (!$request->has('category') && $request->has('categories')) {
            $request->merge(['category' => is_array($request->input('categories')) ? implode(',', $request->input('categories')) : $request->input('categories')]);
        }

        // 2. Validate fields
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string',
            'content'     => 'required|string',
            'image'       => 'nullable',
            'category_id' => 'nullable',
            'category'    => 'nullable|string',
            'tags'        => 'nullable|string',
            'slug'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            SubeditorLog::create([
                'portal_id'     => $portalId,
                'post_id'       => null,
                'title'         => (string) $request->input('title', 'Unknown Title'),
                'category_name' => (string) $request->input('category', ''),
                'status'        => 'failed',
                'url'           => null,
                'error_message' => $validator->errors()->first(),
                'ip_address'    => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $title = trim((string) $request->input('title'));
            $content = (string) $request->input('content');

            // 3. Process image
            $featuredImageData = null;
            $imageUrl = null;

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                $path = Storage::disk('public')->putFile('uploads', $file);
                $imageUrl = Storage::disk('public')->url($path);
                $featuredImageData = [
                    'original'  => $imageUrl,
                    'thumbnail' => $imageUrl,
                    'md'        => $imageUrl,
                ];
            } elseif ($request->filled('image') && is_string($request->input('image'))) {
                $rawImage = trim($request->input('image'));
                if (filter_var($rawImage, FILTER_VALIDATE_URL)) {
                    try {
                        $response = Http::timeout(8)->get($rawImage);
                        if ($response->successful()) {
                            $ext = pathinfo((string) parse_url($rawImage, PHP_URL_PATH), PATHINFO_EXTENSION);
                            if (empty($ext) || strlen($ext) > 5) {
                                $ext = 'jpg';
                            }
                            $filename = 'uploads/' . Str::random(40) . '.' . $ext;
                            Storage::disk('public')->put($filename, $response->body());
                            $imageUrl = Storage::disk('public')->url($filename);
                        } else {
                            $imageUrl = $rawImage;
                        }
                    } catch (\Throwable $e) {
                        $imageUrl = $rawImage;
                    }
                } else {
                    $imageUrl = $rawImage;
                }

                if ($imageUrl) {
                    $featuredImageData = [
                        'original'  => $imageUrl,
                        'thumbnail' => $imageUrl,
                        'md'        => $imageUrl,
                    ];
                }
            }

            // 4. Category resolution
            $category = null;
            if ($request->filled('category_id')) {
                $category = Category::withoutGlobalScopes()
                    ->where('portal_id', $portalId)
                    ->where('id', $request->input('category_id'))
                    ->first();
            }

            if (!$category && $request->filled('category')) {
                $catInput = trim((string) $request->input('category'));
                $category = Category::withoutGlobalScopes()
                    ->where('portal_id', $portalId)
                    ->where(function ($q) use ($catInput) {
                        $q->where('name', $catInput)
                          ->orWhere('name_en', $catInput)
                          ->orWhere('slug', Str::slug($catInput))
                          ->orWhere('slug', $catInput);
                    })
                    ->first();
            }

            if (!$category) {
                $defaultCatId = PortalSetting::where('portal_id', $portalId)
                    ->where('key', 'subeditor_default_category_id')
                    ->value('value')
                    ?? PortalSetting::where('key', 'subeditor_default_category_id')->value('value')
                    ?? 1;

                $category = Category::withoutGlobalScopes()
                    ->where('portal_id', $portalId)
                    ->where('id', $defaultCatId)
                    ->first()
                    ?? Category::withoutGlobalScopes()
                        ->where('portal_id', $portalId)
                        ->where('is_active', true)
                        ->first();
            }

            $categoryId = $category?->id;
            $categoryName = $category?->name ?? (string) $request->input('category', '');

            // 5. Author resolution
            $authorId = PortalSetting::where('portal_id', $portalId)
                ->where('key', 'subeditor_default_author_id')
                ->value('value')
                ?? PortalSetting::where('key', 'subeditor_default_author_id')->value('value')
                ?? User::first()?->id
                ?? 1;

            // 6. Status resolution
            $autoPublishSetting = PortalSetting::where('portal_id', $portalId)
                ->where('key', 'subeditor_auto_publish')
                ->value('value')
                ?? PortalSetting::where('key', 'subeditor_auto_publish')->value('value');

            $isAutoPublish = ($autoPublishSetting === null || in_array($autoPublishSetting, ['1', 1, true, 'true', 'yes'], true));

            $status = $isAutoPublish ? PostStatus::Published : PostStatus::Draft;
            $publishedAt = $isAutoPublish ? now() : null;

            // 7. Slug and Post Creation
            $baseSlug = $request->filled('slug')
                ? Str::slug((string) $request->input('slug'))
                : Str::slug($title);

            if (empty($baseSlug)) {
                $baseSlug = 'news-' . time();
            }

            $slug = $baseSlug;
            $counter = 1;
            while (Post::withoutGlobalScopes()->where('portal_id', $portalId)->where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }

            $excerpt = Str::limit(strip_tags($content), 200);

            $post = Post::withoutGlobalScopes()->create([
                'portal_id'               => $portalId,
                'author_id'               => (int) $authorId,
                'category_id'             => $categoryId,
                'title'                   => $title,
                'slug'                    => $slug,
                'excerpt'                 => $excerpt,
                'body'                    => $content,
                'featured_image'          => $featuredImageData,
                'status'                  => $status,
                'type'                    => PostType::Article,
                'is_breaking'             => false,
                'is_featured'             => false,
                'is_exclusive'            => false,
                'is_sticky'               => false,
                'is_corrected'            => false,
                'allow_comments'          => true,
                'view_count'              => 0,
                'share_count'             => 0,
                'published_at'            => $publishedAt,
            ]);

            // 8. Handle tags
            if ($request->filled('tags')) {
                $rawTags = is_array($request->input('tags'))
                    ? $request->input('tags')
                    : explode(',', (string) $request->input('tags'));

                $tagIds = [];
                foreach ($rawTags as $tagName) {
                    $tagName = trim((string) $tagName);
                    if ($tagName === '') {
                        continue;
                    }
                    $tagSlug = Str::slug($tagName);
                    if (empty($tagSlug)) {
                        $tagSlug = 'tag-' . substr(md5($tagName), 0, 8);
                    }
                    $tag = Tag::withoutGlobalScopes()->firstOrCreate(
                        ['portal_id' => $portalId, 'slug' => $tagSlug],
                        ['name' => $tagName, 'type' => 'tag', 'post_count' => 0]
                    );
                    $tagIds[] = $tag->id;
                }

                if (!empty($tagIds)) {
                    $post->tags()->sync($tagIds);
                }
            }

            // 9. Build live URL
            $frontendUrl = rtrim((string) config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3000')), '/');
            $liveUrl = "{$frontendUrl}/news/{$slug}";

            // 10. Log into subeditor_logs
            SubeditorLog::create([
                'portal_id'     => $portalId,
                'post_id'       => $post->id,
                'title'         => $title,
                'category_name' => $categoryName,
                'status'        => $isAutoPublish ? 'published' : 'draft',
                'url'           => $liveUrl,
                'error_message' => null,
                'ip_address'    => $request->ip(),
            ]);

            // 11. Return JSON format expected by Subeditor24
            return response()->json([
                'success' => true,
                'message' => 'News published successfully',
                'post_id' => $post->id,
                'url'     => $liveUrl,
            ]);

        } catch (\Throwable $e) {
            Log::error('Subeditor news publishing error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            SubeditorLog::create([
                'portal_id'     => $portalId,
                'post_id'       => null,
                'title'         => (string) $request->input('title', 'Unknown Title'),
                'category_name' => (string) $request->input('category', ''),
                'status'        => 'failed',
                'url'           => null,
                'error_message' => $e->getMessage(),
                'ip_address'    => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to publish news: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get categories list for Subeditor24
     */
    public function categories(Request $request): JsonResponse
    {
        $portalId = (int) ($request->header('X-Portal-Id') ?: $request->input('portal_id', 1));

        if (!$this->validateToken($request, $portalId)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $categories = Category::withoutGlobalScopes()
            ->where('is_active', true)
            ->where('portal_id', $portalId)
            ->select('id', 'name')
            ->orderBy('order')
            ->get();

        return response()->json($categories);
    }

    /**
     * Validate secret token
     */
    protected function validateToken(Request $request, int $portalId = 1): bool
    {
        $token = $request->bearerToken()
            ?? $request->header('X-API-KEY')
            ?? $request->input('token')
            ?? $request->input('api_key')
            ?? $request->input('secret');

        if (empty($token)) {
            return false;
        }

        $expectedSecret = PortalSetting::where('portal_id', $portalId)
            ->where('key', 'subeditor_api_secret')
            ->value('value')
            ?? PortalSetting::where('key', 'subeditor_api_secret')->value('value')
            ?? env('SUBEDITOR_API_SECRET');

        if (empty($expectedSecret)) {
            return false;
        }

        return hash_equals((string) $expectedSecret, (string) $token);
    }
}
