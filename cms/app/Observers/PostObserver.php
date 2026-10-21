<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Post;
use App\Services\PostService;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    public function saved(Post $post): void
    {
        $this->clearCache($post);
    }

    public function deleted(Post $post): void
    {
        $this->clearCache($post);
    }

    public function restored(Post $post): void
    {
        $this->clearCache($post);
    }

    public function forceDeleted(Post $post): void
    {
        $this->clearCache($post);
    }

    private function clearCache(Post $post): void
    {
        try {
            app(PostService::class)->clearPostCache($post);
        } catch (\Throwable) {
            // Service fallback
        }

        try {
            Cache::flush();
        } catch (\Throwable) {
            // Cache flush fallback
        }
    }
}
