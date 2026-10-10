<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\AI\Contracts\AiProviderInterface;
use App\Domain\Portal\Contracts\PortalRepositoryInterface;
use App\Domain\Post\Contracts\PostRepositoryInterface;
use App\Infrastructure\AI\OpenAiProvider;
use App\Infrastructure\AI\GeminiProvider;
use App\Infrastructure\Repositories\EloquentPortalRepository;
use App\Infrastructure\Repositories\EloquentPostRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bind interfaces to concrete implementations.
     * To swap an implementation, change only this file.
     */
    public function register(): void
    {
        // Repositories
        $this->app->bind(
            PostRepositoryInterface::class,
            EloquentPostRepository::class,
        );

        $this->app->bind(
            PortalRepositoryInterface::class,
            EloquentPortalRepository::class,
        );

        // AI Provider — resolved from config
        $this->app->bind(AiProviderInterface::class, function () {
            $provider = config('cms.ai.default_provider', 'openai');

            return match ($provider) {
                'gemini' => app(GeminiProvider::class),
                default  => app(OpenAiProvider::class),
            };
        });
    }
}
