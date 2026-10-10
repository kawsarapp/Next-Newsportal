<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Services\PostService;
use App\Domain\Post\ValueObjects\PostStatus;
use App\Domain\Post\ValueObjects\PostType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    public function index(Request $request): Response
    {
        $portalId = auth()->user()->getEffectivePortalId();

        $filters = $request->only(['status', 'search', 'category_id', 'type', 'sort']);
        $posts = $this->postService->paginate($filters, 15);

        $categories = Category::where('portal_id', $portalId)
            ->where('is_active', true)
            ->get(['id', 'name']);

        $counts = $this->postService->countByStatus();

        return Inertia::render('Dashboard/Posts/Index', [
            'posts' => $posts,
            'categories' => $categories,
            'counts' => $counts,
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        $portalId = auth()->user()->getEffectivePortalId();
        $categories = Category::where('portal_id', $portalId)->where('is_active', true)->get(['id', 'name']);


        return Inertia::render('Dashboard/Posts/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'type' => 'nullable|string',
            'status' => 'nullable|string',
            'is_breaking' => 'boolean',
            'is_featured' => 'boolean',
            'is_exclusive' => 'boolean',
            'seo_title' => 'nullable|string|max:70',
            'seo_description' => 'nullable|string|max:255',
            'featured_image' => 'nullable|array',
        ]);

        $post = $this->postService->create($validated, auth()->id());

        if (($validated['status'] ?? 'draft') === 'published') {
            $this->postService->changeStatus($post, PostStatus::Published, auth()->id());
        }

        return redirect()->route('dashboard.posts.index')->with('success', 'সংবাদটি সফলভাবে তৈরি হয়েছে!');
    }

    public function edit(Post $post): Response
    {
        $portalId = auth()->user()->getEffectivePortalId();
        $categories = Category::where('portal_id', $portalId)->where('is_active', true)->get(['id', 'name']);

        return Inertia::render('Dashboard/Posts/Create', [
            'post' => $post->load(['category', 'tags']),
            'categories' => $categories,
        ]);
    }


    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'type' => 'nullable|string',
            'status' => 'nullable|string',
            'is_breaking' => 'boolean',
            'is_featured' => 'boolean',
            'is_exclusive' => 'boolean',
            'seo_title' => 'nullable|string|max:70',
            'seo_description' => 'nullable|string|max:255',
            'featured_image' => 'nullable|array',
        ]);

        $targetStatusStr = $validated['status'] ?? null;
        unset($validated['status']);

        $updatedPost = $this->postService->update($post, $validated, auth()->id());

        if ($targetStatusStr) {
            $newStatus = PostStatus::from($targetStatusStr);
            if ($newStatus !== $updatedPost->status) {
                $this->postService->changeStatus($updatedPost, $newStatus, auth()->id());
            } elseif ($newStatus === PostStatus::Published && empty($updatedPost->published_at)) {
                $updatedPost->update(['published_at' => now()]);
            }
        }

        return redirect()->route('dashboard.posts.index')->with('success', 'সংবাদটি সফলভাবে আপডেট হয়েছে!');
    }

    public function destroy(int|string $id): RedirectResponse
    {
        $post = Post::withoutGlobalScopes()->withTrashed()->findOrFail($id);

        if ($post->trashed()) {
            // Already in trash, permanently delete
            $post->forceDelete();
            return back()->with('success', 'সংবাদটি স্থায়ীভাবে মুছে ফেলা হয়েছে।');
        }

        $this->postService->delete($post);
        return back()->with('success', 'সংবাদটি ট্র্যাশে পাঠানো হয়েছে।');
    }

    public function restore(int|string $id): RedirectResponse
    {
        $post = Post::withoutGlobalScopes()->onlyTrashed()->findOrFail($id);
        $post->restore();

        return back()->with('success', 'সংবাদটি ট্র্যাশ থেকে পুনরুদ্ধার করা হয়েছে।');
    }

    public function approve(Post $post): RedirectResponse
    {
        $this->postService->changeStatus($post, PostStatus::Approved, auth()->id());
        return back()->with('success', 'সংবাদটি অনুমোদিত হয়েছে।');
    }

    public function publish(Post $post): RedirectResponse
    {
        $this->postService->changeStatus($post, PostStatus::Published, auth()->id());
        return back()->with('success', 'সংবাদটি প্রকাশিত হয়েছে।');
    }

    public function toggleBreaking(Post $post): RedirectResponse
    {
        $post->update(['is_breaking' => !$post->is_breaking]);
        return back()->with('success', 'ব্রেকিং স্ট্যাটাস পরিবর্তিত হয়েছে।');
    }

    public function bulk(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:posts,id',
            'action' => 'required|in:publish,archive,trash',
        ]);

        $posts = Post::whereIn('id', $request->ids)->get();

        foreach ($posts as $post) {
            if ($request->action === 'publish') {
                $this->postService->changeStatus($post, PostStatus::Published, auth()->id());
            } elseif ($request->action === 'archive') {
                $this->postService->changeStatus($post, PostStatus::Archived, auth()->id());
            } elseif ($request->action === 'trash') {
                $this->postService->delete($post);
            }
        }

        return back()->with('success', 'নির্বাচিত সংবাদগুলোর উপর প্রক্রিয়া সম্পন্ন হয়েছে।');
    }
}
