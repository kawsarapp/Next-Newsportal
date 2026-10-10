<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Post\ValueObjects\PostStatus;
use App\Domain\Post\ValueObjects\PostType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Scopes\PortalScope;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'portal_id', 'author_id', 'category_id',
        'title', 'slug', 'excerpt', 'body',
        'featured_image', 'status', 'type',
        'is_breaking', 'is_featured', 'is_exclusive', 'is_sticky',
        'is_corrected', 'correction_note', 'allow_comments',
        'breaking_order',
        'seo_title', 'seo_description', 'seo_og_image', 'schema_markup', 'canonical_url',
        'facebook_caption', 'twitter_caption',
        'view_count', 'share_count', 'reading_time_minutes',
        'published_at', 'scheduled_at', 'embargoed_until',
    ];

    protected $casts = [
        'featured_image'  => 'array',
        'schema_markup'   => 'array',
        'status'          => PostStatus::class,
        'type'            => PostType::class,
        'is_breaking'     => 'boolean',
        'is_featured'     => 'boolean',
        'is_exclusive'    => 'boolean',
        'is_sticky'       => 'boolean',
        'is_corrected'    => 'boolean',
        'allow_comments'  => 'boolean',
        'published_at'    => 'datetime',
        'scheduled_at'    => 'datetime',
        'embargoed_until' => 'datetime',
    ];

    // ─── Global Scope (Portal Isolation) ─────────────────────────────────────

    protected static function booted(): void
    {
        static::addGlobalScope(new PortalScope());

        static::creating(function (Post $post) {
            if (empty($post->portal_id)) {
                $post->portal_id = auth()->user()?->getEffectivePortalId()
                    ?? (app()->bound('current.portal') ? app('current.portal')?->id : null)
                    ?? (Portal::first()?->id ?? 1);
            }
        });

        // Auto-calculate reading time before saving
        static::saving(function (Post $post) {
            if ($post->isDirty('body')) {
                $stripped = strip_tags($post->body ?? '');
                $wordCount = preg_match_all('/\p{L}+/u', $stripped);
                $post->reading_time_minutes = max(1, (int) ceil(($wordCount ?: str_word_count($stripped)) / 180));
            }

            if (($post->status === PostStatus::Published || (is_string($post->status) && $post->status === 'published')) && empty($post->published_at)) {
                $post->published_at = now();
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(PostRevision::class)->latest();
    }

    public function workflow(): HasOne
    {
        return $this->hasOne(EditorialWorkflow::class);
    }

    public function editorialNotes(): HasMany
    {
        return $this->hasMany(EditorialNote::class)->latest();
    }

    public function socialPosts(): HasMany
    {
        return $this->hasMany(SocialPost::class);
    }

    // ─── Scopes (Query Filters) ───────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Published)
                     ->where('published_at', '<=', now());
    }

    public function scopeBreaking(Builder $query): Builder
    {
        return $query->where('is_breaking', true)
                     ->where('status', PostStatus::Published)
                     ->orderBy('breaking_order');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)
                     ->where('status', PostStatus::Published);
    }

    public function scopePendingReview(Builder $query): Builder
    {
        return $query->where('status', PostStatus::PendingReview);
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Scheduled)
                     ->where('scheduled_at', '<=', now());
    }

    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByType(Builder $query, PostType $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->whereFullText(['title', 'excerpt'], $term);
    }

    // ─── Business Logic ───────────────────────────────────────────────────────

    public function canTransitionTo(PostStatus $newStatus): bool
    {
        return $this->status->canTransitionTo($newStatus);
    }

    public function isPublished(): bool
    {
        return $this->status === PostStatus::Published;
    }

    public function isDraft(): bool
    {
        return $this->status === PostStatus::Draft;
    }

    public function isScheduled(): bool
    {
        return $this->status === PostStatus::Scheduled;
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function getUrlAttribute(): string
    {
        return "/{$this->category?->slug}/{$this->slug}";
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        return $this->featured_image['md'] ?? $this->featured_image['original'] ?? null;
    }
}
