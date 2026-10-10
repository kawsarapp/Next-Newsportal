<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Portal;
use Illuminate\Support\Facades\Cache;

/**
 * SettingService — 3-layer config fallback
 *
 * Priority: Portal DB Setting → Global config/cms.php → Hardcoded default
 */
class SettingService
{
    public function __construct(private readonly Portal $portal) {}

    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember(
            "portal:{$this->portal->id}:setting:{$key}",
            now()->addHours(24),
            fn () => $this->portal
                ->settings()
                ->where('key', $key)
                ->value('value')
        ) ?? config("cms.defaults.{$key}", $default);
    }

    public function set(string $key, mixed $value, string $type = 'string'): void
    {
        $this->portal->settings()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );

        Cache::forget("portal:{$this->portal->id}:setting:{$key}");
    }

    public function getAll(string $group = null): array
    {
        return Cache::remember(
            "portal:{$this->portal->id}:settings:group:{$group}",
            now()->addHours(24),
            function () use ($group) {
                $query = $this->portal->settings();
                if ($group) {
                    $query->where('group', $group);
                }
                return $query->pluck('value', 'key')->toArray();
            }
        );
    }

    public function getDesignTokens(): array
    {
        return Cache::remember(
            "portal:{$this->portal->id}:design_tokens",
            now()->addHours(24),
            fn () => $this->portal
                ->designTokens()
                ->pluck('token_value', 'token_key')
                ->mapWithKeys(fn ($val, $key) => ["--{$key}" => $val])
                ->toArray()
        );
    }

    public function setDesignToken(string $key, string $value, string $category = 'custom'): void
    {
        $this->portal->designTokens()->updateOrCreate(
            ['token_key' => $key],
            ['token_value' => $value, 'category' => $category]
        );

        Cache::forget("portal:{$this->portal->id}:design_tokens");
    }

    public function forgetAll(): void
    {
        Cache::tags(["portal:{$this->portal->id}"])->flush();
    }
}
