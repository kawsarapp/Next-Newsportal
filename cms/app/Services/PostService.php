<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Post\ValueObjects\PostStatus;
use App\Domain\Post\Events\PostPublished;
use App\Domain\Post\Events\PostStatusChanged;
use App\Models\Post;
use App\Models\Portal;
use App\Models\Category;
use App\Models\PostRevision;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class PostService
{
    private ?Portal $portal;

    public function __construct(?Portal $portal = null)
    {
        $this->portal = $portal;
    }

    public function getPortalId(): int
    {
        if ($this->portal && $this->portal->id) {
            return (int) $this->portal->id;
        }

        if (auth()->check()) {
            return auth()->user()->getEffectivePortalId();
        }

        $appPortal = app()->bound('current.portal') ? app('current.portal') : null;
        if ($appPortal && $appPortal->id) {
            return (int) $appPortal->id;
        }

        return (int) (Portal::first()?->id ?? 1);
    }


    // ─── Read ─────────────────────────────────────────────────────────────────

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Post::withoutGlobalScopes()
                     ->where('portal_id', $this->getPortalId())
                     ->with([
                         'author:id,name,avatar',
                         'category:id,name,slug',
                         'division:id,name,bn_name',
                         'district:id,division_id,name,bn_name',
                     ])
                     ->latest('updated_at');

        if (! empty($filters['status'])) {
            if ($filters['status'] === 'trashed') {
                $query->onlyTrashed();
            } else {
                $query->whereNull('deleted_at')->where('status', $filters['status']);
            }
        } else {
            // Default: do not show soft-deleted posts
            $query->whereNull('deleted_at');
        }

        if (! empty($filters['category_id'])) {
            $catId = (int) $filters['category_id'];
            $childrenIds = Category::where('parent_id', $catId)->pluck('id');
            $grandChildrenIds = Category::whereIn('parent_id', $childrenIds)->pluck('id');
            $allIds = $childrenIds->merge($grandChildrenIds)->push($catId)->unique()->all();
            $query->where(function ($q) use ($allIds) {
                $q->whereIn('category_id', $allIds)
                  ->orWhereHas('secondaryCategories', fn ($sq) => $sq->whereIn('categories.id', $allIds));
            });
        }
        if (! empty($filters['division_id'])) {
            $query->where('division_id', $filters['division_id']);
        }
        if (! empty($filters['district_id'])) {
            $query->where('district_id', $filters['district_id']);
        }
        if (! empty($filters['author_id'])) {
            $query->where('author_id', $filters['author_id']);
        }
        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('excerpt', 'like', "%{$filters['search']}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function getPublishedForApi(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $categorySlug = $filters['category'] ?? null;
        $categoryIds = [];

        if (! empty($filters['category_id'])) {
            $catId = (int) $filters['category_id'];
            $childrenIds = Category::where('parent_id', $catId)->pluck('id');
            $grandChildrenIds = Category::whereIn('parent_id', $childrenIds)->pluck('id');
            $categoryIds = $childrenIds->merge($grandChildrenIds)->push($catId)->unique()->all();
        } elseif (! empty($categorySlug)) {
            $cat = Category::where('slug', $categorySlug)->first();
            if ($cat) {
                $childrenIds = Category::where('parent_id', $cat->id)->pluck('id');
                $grandChildrenIds = Category::whereIn('parent_id', $childrenIds)->pluck('id');
                $categoryIds = $childrenIds->merge($grandChildrenIds)->push($cat->id)->unique()->all();
            }
        }

        return Post::published()
                   ->with([
                       'author:id,name,avatar',
                       'category:id,name,slug',
                       'division:id,name,bn_name',
                       'district:id,division_id,name,bn_name',
                       'secondaryCategories:id,name,slug',
                       'tags:id,name,slug',
                   ])
                   ->when(! empty($categoryIds), function ($q) use ($categoryIds) {
                       $q->where(function ($sq) use ($categoryIds) {
                           $sq->whereIn('category_id', $categoryIds)
                              ->orWhereHas('secondaryCategories', fn ($scq) => $scq->whereIn('categories.id', $categoryIds));
                           foreach ($categoryIds as $cid) {
                               $sq->orWhereJsonContains('additional_category_ids', (int) $cid)
                                  ->orWhereJsonContains('additional_category_ids', (string) $cid);
                           }
                       });
                   })
                   ->when(empty($categoryIds) && ! empty($categorySlug), function ($q) use ($categorySlug) {
                       $q->where(function ($sq) use ($categorySlug) {
                           $sq->whereHas('category', fn ($c) => $c->where('slug', $categorySlug))
                              ->orWhereHas('secondaryCategories', fn ($c) => $c->where('slug', $categorySlug));
                       });
                   })
                   ->when(! empty($filters['division_id']), fn ($q) => $q->where('division_id', $filters['division_id']))
                   ->when(! empty($filters['district_id']), fn ($q) => $q->where('district_id', $filters['district_id']))
                   ->when(! empty($filters['division']), function ($q) use ($filters) {
                       $val = $filters['division'];
                       if (is_numeric($val)) {
                           $q->where('division_id', $val);
                       } else {
                           $q->whereHas('division', fn ($dq) => $dq->where('name', $val)->orWhere('name_en', $val)->orWhere('bn_name', $val));
                       }
                   })
                   ->when(! empty($filters['district']), function ($q) use ($filters) {
                       $val = $filters['district'];
                       if (is_numeric($val)) {
                           $q->where('district_id', $val);
                       } else {
                           $q->whereHas('district', fn ($dq) => $dq->where('name', $val)->orWhere('name_en', $val)->orWhere('bn_name', $val));
                       }
                   })
                   ->when(! empty($filters['tag']),      fn ($q) => $q->whereHas('tags', fn ($t) => $t->where('slug', $filters['tag'])))
                   ->when(! empty($filters['type']),     fn ($q) => $q->where('type', $filters['type']))
                   ->when(! empty($filters['search']), function ($q) use ($filters) {
                       $search = trim($filters['search']);
                       $q->where(function ($sq) use ($search) {
                           $sq->where('title', 'like', "%{$search}%")
                              ->orWhere('excerpt', 'like', "%{$search}%");
                       });
                   })
                   ->latest('published_at')
                   ->paginate($perPage);
    }

    public function getBreaking(int $limit = 20): \Illuminate\Support\Collection
    {
        $breaking = Post::breaking()
            ->with('category:id,name,slug')
            ->latest('published_at')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'published_at', 'is_breaking', 'category_id']);

        $needed = $limit - $breaking->count();
        if ($needed > 0) {
            $latest = Post::published()
                ->whereNotIn('id', $breaking->pluck('id'))
                ->with('category:id,name,slug')
                ->latest('published_at')
                ->limit($needed)
                ->get(['id', 'title', 'slug', 'published_at', 'is_breaking', 'category_id']);
            $breaking = $breaking->merge($latest);
        }

        return $breaking;
    }

    public function getFeatured(int $limit = 10): \Illuminate\Support\Collection
    {
        return Post::featured()
                   ->with([
                       'author:id,name,avatar',
                       'category:id,name,slug',
                       'division:id,name,bn_name',
                       'district:id,division_id,name,bn_name',
                       'secondaryCategories:id,name,slug',
                       'tags:id,name,slug',
                   ])
                   ->latest('published_at')
                   ->limit($limit)
                   ->get();
    }

    public function findBySlug(string $slug): ?Post
    {
        // Pre-load model classes so PHP unserialize from file/redis cache never encounters __PHP_Incomplete_Class
        class_exists(Post::class);
        class_exists(\App\Models\Category::class);
        class_exists(\App\Models\Division::class);
        class_exists(\App\Models\District::class);
        class_exists(\App\Models\User::class);
        class_exists(\App\Models\Tag::class);

        $portalId = $this->getPortalId();
        $decoded = trim(urldecode($slug));
        $cacheKey = "portal:{$portalId}:post:slug:" . md5($decoded !== '' ? $decoded : $slug);

        $cached = Cache::get($cacheKey);
        if ($cached instanceof Post) {
            return $cached;
        }

        $relations = [
            'author:id,name,avatar',
            'category:id,name,slug',
            'division:id,name,bn_name',
            'district:id,division_id,name,bn_name',
            'secondaryCategories:id,name,slug',
            'tags:id,name,slug',
        ];

        // 1. Try matching slug directly (exact or decoded)
        $post = Post::published()->where('slug', $slug)->with($relations)->first();
        if (! $post && $decoded !== $slug) {
            $post = Post::published()->where('slug', $decoded)->with($relations)->first();
        }

        // 2. Fallback: match by title (exact or like) or ID if numeric
        if (! $post) {
            $post = Post::published()->where('title', $decoded)->with($relations)->first()
                ?? Post::published()->where('title', 'like', "%{$decoded}%")->with($relations)->first();

            if (! $post && is_numeric($decoded)) {
                $post = Post::published()->where('id', (int) $decoded)->with($relations)->first();
            }
        }

        if ($post) {
            Cache::put($cacheKey, $post, now()->addHours(6));
            Cache::put("portal:{$portalId}:post:slug:{$post->slug}", $post, now()->addHours(6));
            Cache::put("portal:{$portalId}:post:slug:" . md5($post->slug), $post, now()->addHours(6));
        }

        return $post;
    }

    // ─── Write ────────────────────────────────────────────────────────────────

    public function create(array $data, int $authorId): Post
    {
        $portalId          = $this->getPortalId();
        $data['portal_id'] = $portalId;
        $data['author_id'] = $authorId;
        $data['slug']      = $this->generateUniqueSlug($data['title']);
        $data['status']    = PostStatus::Draft->value;

        $secondaryCategoryIds = $data['secondary_category_ids'] ?? [];
        unset($data['secondary_category_ids']);

        $post = DB::transaction(function () use ($data, $portalId, $secondaryCategoryIds) {
            $post = Post::withoutGlobalScopes()->create($data);

            if (! empty($secondaryCategoryIds)) {
                $post->secondaryCategories()->sync($secondaryCategoryIds);
                $post->update(['additional_category_ids' => array_values($secondaryCategoryIds)]);
            }

            // Create editorial workflow
            $post->workflow()->create([
                'portal_id'     => $portalId,
                'current_stage' => 'draft',
            ]);

            return $post;
        });

        $this->clearPostCache($post);

        return $post;
    }


    public function update(Post $post, array $data, int $editorId): Post
    {
        // Save revision before update
        $this->saveRevision($post, $data, $editorId);

        if (($data['status'] ?? null) === 'published' && empty($post->published_at) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $secondaryCategoryIds = $data['secondary_category_ids'] ?? null;
        unset($data['secondary_category_ids']);

        if ($secondaryCategoryIds !== null) {
            $post->secondaryCategories()->sync($secondaryCategoryIds);
            $data['additional_category_ids'] = array_values($secondaryCategoryIds);
        }

        $post->update($data);
        $this->clearPostCache($post);

        return $post->fresh();
    }

    public function changeStatus(Post $post, PostStatus $newStatus, int $byUserId, ?string $note = null): Post
    {
        $oldStatus = $post->status;

        // Enforce state machine
        throw_unless(
            $oldStatus->canTransitionTo($newStatus),
            new \DomainException("Cannot transition post from [{$oldStatus->value}] to [{$newStatus->value}]")
        );

        DB::transaction(function () use ($post, $newStatus, $byUserId, $note, $oldStatus) {
            $post->update(['status' => $newStatus->value]);

            // Update workflow stage
            $post->workflow?->update(['current_stage' => $newStatus->value]);

            // Log the transition
            $post->workflow?->transitions()->create([
                'post_id'     => $post->id,
                'from_stage'  => $oldStatus->value,
                'to_stage'    => $newStatus->value,
                'by_user_id'  => $byUserId,
                'note'        => $note,
                'ip_address'  => request()->ip(),
            ]);

            // Fire events
            Event::dispatch(new PostStatusChanged(
                postId:     $post->id,
                portalId:   (int) $post->portal_id,
                fromStatus: $oldStatus->value,
                toStatus:   $newStatus->value,
                changedBy:  $byUserId,
                note:       $note,
            ));

            if ($newStatus === PostStatus::Published) {
                $post->update(['published_at' => now()]);
                Event::dispatch(new PostPublished(
                    postId:      $post->id,
                    portalId:    (int) $post->portal_id,
                    publishedBy: $byUserId,
                ));
            }
        });


        $this->clearPostCache($post);

        return $post->fresh();
    }

    public function delete(Post $post): void
    {
        $post->delete(); // SoftDelete
        $this->clearPostCache($post);
    }

    // ─── Dashboard Stats ──────────────────────────────────────────────────────

    public function countByStatus(): array
    {
        $portalId = $this->getPortalId();

        $counts = Post::withoutGlobalScopes()
                     ->where('portal_id', $portalId)
                     ->whereNull('deleted_at')
                     ->selectRaw('status, COUNT(*) as count')
                     ->groupBy('status')
                     ->pluck('count', 'status')
                     ->toArray();

        $counts['trashed'] = Post::withoutGlobalScopes()
                                 ->where('portal_id', $portalId)
                                 ->onlyTrashed()
                                 ->count();

        $counts[''] = array_sum(array_diff_key($counts, ['trashed' => 0]));

        return $counts;
    }


    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function generateUniqueSlug(string $title): string
    {
        $portalId = $this->getPortalId();
        $slug     = \App\Domain\Post\ValueObjects\Slug::fromTitle($title)->value();
        $count    = Post::withoutGlobalScopes()
                       ->where('portal_id', $portalId)
                       ->where('slug', 'like', "{$slug}%")
                       ->count();

        return $count > 0 ? "{$slug}-{$count}" : $slug;
    }

    private function saveRevision(Post $post, array $newData, int $editorId): void
    {
        PostRevision::create([
            'post_id'     => $post->id,
            'editor_id'   => $editorId,
            'old_content' => $post->only(['title', 'body', 'excerpt', 'status', 'category_id']),
            'new_content' => array_intersect_key($newData, array_flip(['title', 'body', 'excerpt', 'status', 'category_id'])),
            'editor_ip'   => request()->ip(),
            'created_at'  => now(),
        ]);
    }

    public function clearPostCache(Post $post): void
    {
        $portalId = $this->getPortalId();
        Cache::forget("portal:{$portalId}:post:slug:{$post->slug}");
        Cache::forget("portal:{$portalId}:post:slug:" . md5($post->slug));
        if (! empty($post->title)) {
            Cache::forget("portal:{$portalId}:post:slug:" . md5($post->title));
        }
        Cache::forget("portal:{$portalId}:post_counts");
        Cache::forget("portal:{$portalId}:breaking:5");
        Cache::forget("portal:{$portalId}:breaking:10");
        Cache::forget("portal:{$portalId}:breaking:15");
        Cache::forget("portal:{$portalId}:breaking:20");

        try {
            Cache::increment("portal:{$portalId}:posts_version");
        } catch (\Throwable) {
            Cache::put("portal:{$portalId}:posts_version", time(), now()->addDays(30));
        }
    }
}

