<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;

class EditorialController extends Controller
{
    public function index(): Response
    {
        $portalId = auth()->user()->getEffectivePortalId();

        $posts = Post::withoutGlobalScopes()
            ->where('portal_id', $portalId)
            ->with(['author:id,name', 'category:id,name'])
            ->latest('updated_at')
            ->limit(60)
            ->get();

        return Inertia::render('Dashboard/Editorial/Index', [
            'posts' => $posts,
        ]);
    }

    public function myStories(): Response
    {
        $userId = auth()->id();
        $posts = Post::withoutGlobalScopes()
            ->where('author_id', $userId)
            ->with(['category:id,name'])
            ->latest('updated_at')
            ->get();

        return Inertia::render('Dashboard/Editorial/Index', [
            'posts' => $posts,
        ]);
    }

    public function reviewQueue(): Response
    {
        $portalId = auth()->user()->getEffectivePortalId();

        $posts = Post::withoutGlobalScopes()
            ->where('portal_id', $portalId)
            ->where('status', 'pending_review')
            ->with(['author:id,name', 'category:id,name'])
            ->latest('updated_at')
            ->get();

        return Inertia::render('Dashboard/Editorial/Index', [
            'posts' => $posts,
        ]);
    }

    public function factCheck(): Response
    {
        return $this->reviewQueue();
    }
}
