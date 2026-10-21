<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind RepositoryServiceProvider
        $this->app->register(RepositoryServiceProvider::class);
    }

    public function boot(): void
    {
        if (config('app.env') === 'production' || env('FORCE_HTTPS', false) || request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || request()->header('X-Forwarded-Proto') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Super admin bypasses all permission checks
        Gate::before(function ($user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        // Configure Rate Limiters
        \Illuminate\Support\Facades\RateLimiter::for('public_api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(120)->by($request->header('X-Portal-Key') ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('search', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(30)->by($request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('media_upload', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(15)->by($request->user()?->id ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('ai_requests', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
        });

        // Bootstrap Dynamic Modules / Plugins
        $this->app->make(\App\Services\ModuleManager::class)->bootstrapModules();
    }


}
