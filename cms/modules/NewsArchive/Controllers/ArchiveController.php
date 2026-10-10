<?php

declare(strict_types=1);

namespace Modules\NewsArchive\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ArchiveController extends Controller
{
    public function index(Request $request): Response
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $date = $request->query('date', now()->format('Y-m-d'));
        $categoryId = $request->query('category_id');
        $search = $request->query('q');

        $query = Post::withoutGlobalScopes()
            ->where('status', 'published')
            ->whereDate('published_at', $date)
            ->with(['category:id,name,slug', 'author:id,name']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $posts = $query->latest('published_at')->paginate(20)->withQueryString();

        // Monthly breakdown stats for the selected month
        $selectedMonth = substr($date, 0, 7); // YYYY-MM
        $dailyCounts = Post::withoutGlobalScopes()
            ->where('status', 'published')
            ->where('published_at', 'like', "{$selectedMonth}%")
            ->select(DB::raw('DATE(published_at) as publish_date'), DB::raw('count(*) as count'))
            ->groupBy('publish_date')
            ->pluck('count', 'publish_date')
            ->toArray();

        $totalArchived = Post::withoutGlobalScopes()->where('status', 'published')->count();
        $categories = Category::all(['id', 'name']);

        return Inertia::render('Dashboard/Modules/NewsArchive/Index', [
            'posts' => $posts,
            'categories' => $categories,
            'dailyCounts' => $dailyCounts,
            'totalArchived' => $totalArchived,
            'filters' => [
                'date' => $date,
                'category_id' => $categoryId,
                'q' => $search,
            ]
        ]);
    }

    public function searchApi(Request $request): JsonResponse
    {
        $date = $request->query('date', now()->format('Y-m-d'));
        $categoryId = $request->query('category_id');
        $search = $request->query('q');

        $query = Post::withoutGlobalScopes()
            ->where('status', 'published')
            ->whereDate('published_at', $date)
            ->with(['category:id,name,slug', 'author:id,name']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $posts = $query->latest('published_at')->paginate(15);

        return response()->json([
            'success' => true,
            'date' => $date,
            'data' => $posts,
        ]);
    }

    public function calendarStats(Request $request): JsonResponse
    {
        $month = $request->query('month', now()->format('Y-m')); // e.g. "2026-10"

        $dailyCounts = Post::withoutGlobalScopes()
            ->where('status', 'published')
            ->where('published_at', 'like', "{$month}%")
            ->select(DB::raw('DATE(published_at) as publish_date'), DB::raw('count(*) as total'))
            ->groupBy('publish_date')
            ->get();

        return response()->json([
            'success' => true,
            'month' => $month,
            'stats' => $dailyCounts,
        ]);
    }
}
