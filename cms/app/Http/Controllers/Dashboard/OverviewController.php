<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Domain\Post\ValueObjects\PostStatus;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    public function index(): Response
    {
        $portalId = auth()->user()->getEffectivePortalId();

        $data = Cache::remember(
            "portal:{$portalId}:dashboard:overview",
            now()->addMinutes(5),
            fn () => $this->buildDashboardData($portalId)
        );

        return Inertia::render('Dashboard/Overview', $data);
    }

    public function stats(Request $request): \Illuminate\Http\JsonResponse
    {
        $portalId = auth()->user()->getEffectivePortalId();


        return $this->success([
            'live_visitors' => rand(10, 200), // integrate with analytics
            'today_posts'   => Post::withoutGlobalScopes()
                ->where('portal_id', $portalId)
                ->whereDate('created_at', today())
                ->count(),
        ]);
    }

    private function buildDashboardData(?int $portalId): array
    {
        $baseQuery = fn () => Post::withoutGlobalScopes()->where('portal_id', $portalId);

        // Stat cards
        $todayPosts     = $baseQuery()->whereDate('created_at', today())->count();
        $publishedToday = $baseQuery()->whereDate('published_at', today())->where('status', 'published')->count();
        $pendingCount   = $baseQuery()->where('status', 'pending_review')->count();
        $totalViews     = $baseQuery()->whereDate('published_at', today())->sum('view_count');

        // Pending posts (for approval)
        $pendingPosts = $baseQuery()
            ->where('status', PostStatus::PendingReview->value)
            ->with(['author:id,name', 'category:id,name'])
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn ($p) => [
                'id'            => $p->id,
                'title'         => $p->title,
                'author'        => $p->author?->name,
                'category'      => $p->category?->name,
                'thumbnail'     => $p->featured_image_url,
                'submitted_ago' => $p->updated_at->diffForHumans(),
            ]);

        // Recent published
        $recentPosts = $baseQuery()
            ->where('status', 'published')
            ->with(['category:id,name'])
            ->latest('published_at')
            ->limit(8)
            ->get()
            ->map(fn ($p) => [
                'id'            => $p->id,
                'title'         => $p->title,
                'category'      => $p->category?->name ?? 'অবর্গীকৃত',
                'views'         => $p->view_count,
                'published_ago' => $p->published_at?->diffForHumans(),
            ]);

        // Breaking news
        $breakingNews = $baseQuery()
            ->where('is_breaking', true)
            ->where('status', 'published')
            ->orderBy('breaking_order')
            ->limit(5)
            ->get(['id', 'title']);

        // Status counts
        $rawCounts = $baseQuery()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $statusCounts = [
            ['label' => 'প্রকাশিত',   'count' => $rawCounts['published'] ?? 0,       'color' => 'green'],
            ['label' => 'অনুমোদন বাকি','count' => $rawCounts['pending_review'] ?? 0, 'color' => 'yellow'],
            ['label' => 'ড্রাফট',      'count' => $rawCounts['draft'] ?? 0,           'color' => 'gray'],
            ['label' => 'শিডিউল',      'count' => $rawCounts['scheduled'] ?? 0,       'color' => 'purple'],
            ['label' => 'সংরক্ষিত',   'count' => $rawCounts['archived'] ?? 0,        'color' => 'blue'],
        ];

        // Top reporters this month
        $topReporters = User::withoutGlobalScopes()
            ->where('portal_id', $portalId)
            ->withCount(['posts' => fn ($q) => $q->whereMonth('created_at', now()->month)])
            ->orderByDesc('posts_count')
            ->limit(5)
            ->get(['id', 'name'])
            ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name, 'posts' => $u->posts_count]);

        return [
            'stats' => [
                ['label' => 'আজকের নিউজ',     'value' => $todayPosts,     'icon' => '📝', 'color' => 'blue'],
                ['label' => 'আজকে প্রকাশিত',   'value' => $publishedToday, 'icon' => '✅', 'color' => 'green'],
                ['label' => 'অনুমোদন বাকি',    'value' => $pendingCount,   'icon' => '⏳', 'color' => 'yellow'],
                ['label' => 'আজকের মোট ভিউ',   'value' => $totalViews,     'icon' => '👁️', 'color' => 'red'],
            ],
            'pendingPosts' => $pendingPosts,
            'recentPosts'  => $recentPosts,
            'breakingNews' => $breakingNews,
            'statusCounts' => $statusCounts,
            'topReporters' => $topReporters,
        ];
    }
}
