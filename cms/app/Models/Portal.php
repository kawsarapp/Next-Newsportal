<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Portal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'domain',
        'api_key_prefix', 'api_key_hash', 'api_key_old_hash', 'api_key_rotated_at',
        'tagline', 'logo_path', 'favicon_path',
        'language', 'timezone', 'plan', 'status',
        'trial_ends_at', 'settings',
    ];

    protected $casts = [
        'settings'         => 'array',
        'trial_ends_at'    => 'datetime',
        'api_key_rotated_at' => 'datetime',
    ];

    protected $hidden = [
        'api_key_hash',
        'api_key_old_hash',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(PortalSetting::class);
    }

    public function designTokens(): HasMany
    {
        return $this->hasMany(PortalDesignToken::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(PortalModule::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function adSlots(): HasMany
    {
        return $this->hasMany(AdSlot::class);
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    // ─── Business Logic ───────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function moduleEnabled(string $module): bool
    {
        $cached = cache()->remember(
            "portal:{$this->id}:module:{$module}",
            now()->addHours(24),
            fn () => $this->modules()->where('module', $module)->value('enabled')
        );

        // Fall back to global config default
        if ($cached === null) {
            return (bool) config("cms.modules.{$module}", false);
        }

        return (bool) $cached;
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return cache()->remember(
            "portal:{$this->id}:setting:{$key}",
            now()->addHours(24),
            fn () => $this->settings()->where('key', $key)->value('value')
        ) ?? config("cms.defaults.{$key}", $default);
    }

    public function getPlanLimits(): array
    {
        return config("cms.plans.{$this->plan}", config('cms.plans.basic'));
    }
}
