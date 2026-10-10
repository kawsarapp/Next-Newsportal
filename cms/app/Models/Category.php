<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\PortalScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'portal_id', 'parent_id', 'depth',
        'name', 'name_en', 'slug', 'description',
        'icon', 'color', 'thumbnail', 'type',
        'meta_title', 'meta_description', 'og_image',
        'order', 'is_active', 'show_in_nav',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'show_in_nav'  => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new PortalScope());

        static::creating(function ($model) {
            if (empty($model->portal_id)) {
                $model->portal_id = auth()->user()?->getEffectivePortalId()
                    ?? (app()->bound('current.portal') ? app('current.portal')?->id : null)
                    ?? (Portal::first()?->id ?? 1);
            }
        });
    }


    // ─── Relationships ────────────────────────────────────────────────────────

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeNavigation(Builder $query): Builder
    {
        return $query->where('show_in_nav', true)->orderBy('order');
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }
}
