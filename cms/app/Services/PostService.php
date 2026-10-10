<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Post\ValueObjects\PostStatus;
use App\Domain\Post\Events\PostPublished;
use App\Domain\Post\Events\PostStatusChanged;
use App\Models\Post;
use App\Models\Portal;
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
                     ->with(['author:id,name,avatar', 'category:id,name,slug'])
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
            $query->where('category_id', $filters['category_id']);
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
        return Post::published()
                   ->with(['author:id,name,avatar', 'category:id,name,slug', 'tags:id,name,slug'])
                   ->when(! empty($filters['category']), fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $filters['category'])))
                   ->when(! empty($filters['tag']),      fn ($q) => $q->whereHas('tags', fn ($t) => $t->where('slug', $filters['tag'])))
                   ->when(! empty($filters['type']),     fn ($q) => $q->where('type', $filters['type']))
                   ->latest('published_at')
                   ->paginate($perPage);
    }

    public function getBreaking(int $limit = 5): \Illuminate\Support\Collection
    {
        return Post::breaking()->limit($limit)->get(['id', 'title', 'slug', 'published_at']);
    }

    public function getFeatured(int $limit = 10): \Illuminate\Support\Collection
    {
        return Post::featured()
                   ->with(['author:id,name,avatar', 'category:id,name,slug', 'tags:id,name,slug'])
                   ->latest('published_at')
                   ->limit($limit)
                   ->get();
    }

    public function findBySlug(string $slug): ?Post
    {
        return Post::published()
                     ->where('slug', $slug)
                     ->with(['author', 'category', 'tags'])
                     ->first();
    }

    // ─── Write ────────────────────────────────────────────────────────────────

    public function create(array $data, int $authorId): Post
    {
        $portalId          = $this->getPortalId();
        $data['portal_id'] = $portalId;
        $data['author_id'] = $authorId;
        $data['slug']      = $this->generateUniqueSlug($data['title']);
        $data['status']    = PostStatus::Draft->value;

        return DB::transaction(function () use ($data, $portalId) {
            $post = Post::withoutGlobalScopes()->create($data);

            // Create editorial workflow
            $post->workflow()->create([
                'portal_id'     => $portalId,
                'current_stage' => 'draft',
            ]);

            return $post;
        });
    }


    public function update(Post $post, array $data, int $editorId): Post
    {
        // Save revision before update
        $this->saveRevision($post, $data, $editorId);

        if (($data['status'] ?? null) === 'published' && empty($post->published_at) && empty($data['published_at'])) {
            $data['published_at'] = now();
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

    private function clearPostCache(Post $post): void
    {
        $portalId = $this->getPortalId();
        Cache::forget("portal:{$portalId}:post:slug:{$post->slug}");
        Cache::forget("portal:{$portalId}:post_counts");
        Cache::forget("portal:{$portalId}:breaking:5");
        Cache::forget("portal:{$portalId}:breaking:10");

        try {
            Cache::increment("portal:{$portalId}:posts_version");
        } catch (\Throwable) {
            Cache::put("portal:{$portalId}:posts_version", time(), now()->addDays(30));
        }
    }
}

