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
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    public function index(Request $request): Response
    {
        $portalId = auth()->user()->getEffectivePortalId();

        $filters = $request->only(['status', 'search', 'category_id', 'division_id', 'district_id', 'type', 'sort']);
        $posts = $this->postService->paginate($filters, 15);

        $categories = $this->getHierarchicalCategories($portalId);
        $divisions = \App\Models\Division::with(['districts' => fn ($q) => $q->orderBy('id')])->orderBy('id')->get();

        $counts = $this->postService->countByStatus();

        return Inertia::render('Dashboard/Posts/Index', [
            'posts'      => $posts,
            'categories' => $categories,
            'divisions'  => $divisions,
            'counts'     => $counts,
            'filters'    => $filters,
        ]);
    }

    public function create(): Response
    {
        $portalId = auth()->user()->getEffectivePortalId();
        $categories = $this->getHierarchicalCategories($portalId);
        $existingTags = \App\Models\Tag::where('portal_id', $portalId)
            ->orderByDesc('post_count')
            ->limit(60)
            ->get(['id', 'name', 'slug']);
        $divisions = \App\Models\Division::with(['districts' => fn ($q) => $q->orderBy('id')])
            ->orderBy('id')
            ->get(['id', 'name', 'name_en', 'bn_name']);

        return Inertia::render('Dashboard/Posts/Create', [
            'categories'   => $categories,
            'existingTags' => $existingTags,
            'divisions'    => $divisions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'                    => 'required|string|max:255',
            'excerpt'                  => 'nullable|string|max:500',
            'body'                     => 'required|string',
            'category_id'              => 'required|exists:categories,id',
            'division_id'              => 'nullable|integer|exists:divisions,id',
            'district_id'              => 'nullable|integer|exists:districts,id',
            'secondary_category_ids'   => 'nullable|array',
            'secondary_category_ids.*' => 'integer|exists:categories,id',
            'tags'                     => 'nullable|array',
            'tags.*'                   => 'string|max:60',
            'type'                     => 'nullable|string',
            'status'                   => 'nullable|string',
            'is_breaking'              => 'boolean',
            'is_featured'              => 'boolean',
            'is_exclusive'             => 'boolean',
            'seo_title'                => 'nullable|string|max:70',
            'seo_description'          => 'nullable|string|max:255',
            'featured_image'           => 'nullable|array',
        ]);

        $tagsInput = $validated['tags'] ?? null;
        unset($validated['tags']);

        $portalId = auth()->user()->getEffectivePortalId();
        $post = $this->postService->create($validated, auth()->id());

        $this->syncTags($post, $tagsInput, $portalId);

        if (($validated['status'] ?? 'draft') === 'published') {
            $this->postService->changeStatus($post, PostStatus::Published, auth()->id());
        }

        return redirect()->route('dashboard.posts.index')->with('success', 'সংবাদটি সফলভাবে তৈরি হয়েছে!');
    }

    public function edit(Post $post): Response
    {
        $portalId = auth()->user()->getEffectivePortalId();
        $categories = $this->getHierarchicalCategories($portalId);
        $existingTags = \App\Models\Tag::where('portal_id', $portalId)
            ->orderByDesc('post_count')
            ->limit(60)
            ->get(['id', 'name', 'slug']);
        $divisions = \App\Models\Division::with(['districts' => fn ($q) => $q->orderBy('id')])
            ->orderBy('id')
            ->get(['id', 'name', 'name_en', 'bn_name']);

        return Inertia::render('Dashboard/Posts/Create', [
            'post'         => $post->load(['category', 'division', 'district', 'secondaryCategories', 'tags']),
            'categories'   => $categories,
            'existingTags' => $existingTags,
            'divisions'    => $divisions,
        ]);
    }


    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'title'                    => 'required|string|max:255',
            'excerpt'                  => 'nullable|string|max:500',
            'body'                     => 'required|string',
            'category_id'              => 'required|exists:categories,id',
            'division_id'              => 'nullable|integer|exists:divisions,id',
            'district_id'              => 'nullable|integer|exists:districts,id',
            'secondary_category_ids'   => 'nullable|array',
            'secondary_category_ids.*' => 'integer|exists:categories,id',
            'tags'                     => 'nullable|array',
            'tags.*'                   => 'string|max:60',
            'type'                     => 'nullable|string',
            'status'                   => 'nullable|string',
            'is_breaking'              => 'boolean',
            'is_featured'              => 'boolean',
            'is_exclusive'             => 'boolean',
            'seo_title'                => 'nullable|string|max:70',
            'seo_description'          => 'nullable|string|max:255',
            'featured_image'           => 'nullable|array',
        ]);

        $tagsInput = $validated['tags'] ?? null;
        unset($validated['tags']);

        $targetStatusStr = $validated['status'] ?? null;
        unset($validated['status']);

        $updatedPost = $this->postService->update($post, $validated, auth()->id());

        $portalId = auth()->user()->getEffectivePortalId();
        $this->syncTags($updatedPost, $tagsInput, $portalId);

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
        $this->postService->clearPostCache($post);

        return back()->with('success', 'সংবাদটি ট্র্যাশ থেকে পুনরুদ্ধার করা হয়েছে।');
    }

    private function syncTags(Post $post, ?array $tagNames, int $portalId): void
    {
        if ($tagNames === null) {
            return;
        }

        $tagIds = [];
        foreach ($tagNames as $name) {
            $cleanName = trim(ltrim((string) $name, '#'));
            if ($cleanName === '') {
                continue;
            }

            $slug = \Illuminate\Support\Str::slug($cleanName);
            if (empty($slug)) {
                $slug = preg_replace('/[^\p{L}\p{N}]+/u', '-', $cleanName);
                $slug = trim($slug, '-');
            }
            if (empty($slug)) {
                $slug = 'tag-' . substr(md5($cleanName), 0, 8);
            }

            $tag = \App\Models\Tag::firstOrCreate(
                ['portal_id' => $portalId, 'name' => $cleanName],
                ['slug' => $slug, 'type' => 'tag']
            );

            $tagIds[] = $tag->id;
        }

        $post->tags()->sync($tagIds);

        // Update tag post counts
        if (! empty($tagIds)) {
            foreach ($tagIds as $tId) {
                \App\Models\Tag::where('id', $tId)->update([
                    'post_count' => \Illuminate\Support\Facades\DB::table('post_tag')->where('tag_id', $tId)->count(),
                ]);
            }
        }
    }

    public function submitForReview(Post $post): RedirectResponse
    {
        $this->postService->changeStatus($post, PostStatus::PendingReview, auth()->id());
        return back()->with('success', 'সংবাদটি পর্যালোচনার জন্য জমা দেওয়া হয়েছে।');
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

    public function unpublish(Post $post): RedirectResponse
    {
        $this->postService->changeStatus($post, PostStatus::Draft, auth()->id());
        return back()->with('success', 'সংবাদটি অপ্রকাশিত করা হয়েছে।');
    }

    public function returnForRevision(Post $post, Request $request): RedirectResponse
    {
        $note = $request->input('note');
        $this->postService->changeStatus($post, PostStatus::ReturnedRevision, auth()->id(), $note);
        return back()->with('success', 'সংবাদটি সংশোধনের জন্য ফেরত পাঠানো হয়েছে।');
    }

    public function schedule(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'scheduled_at' => 'required|date',
        ]);

        $post->update(['scheduled_at' => $validated['scheduled_at']]);
        $this->postService->changeStatus($post, PostStatus::Scheduled, auth()->id());

        return back()->with('success', 'সংবাদটি প্রকাশের সময় নির্ধারিত হয়েছে।');
    }

    public function revisions(Post $post, Request $request): JsonResponse|Response
    {
        $revisions = $post->revisions()->with('user:id,name')->get();

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success'   => true,
                'data'      => $revisions,
                'revisions' => $revisions,
            ]);
        }

        $portalId = auth()->user()->getEffectivePortalId();
        $categories = $this->getHierarchicalCategories($portalId);

        return Inertia::render('Dashboard/Posts/Create', [
            'post'       => $post->load(['category', 'tags']),
            'categories' => $categories,
            'revisions'  => $revisions,
        ]);
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

    /**
     * Recursively sort categories into a clean hierarchical list for dropdowns/filters.
     */
    private function getHierarchicalCategories(int|string|null $portalId): array
    {
        $categories = Category::where('portal_id', $portalId)
            ->where('is_active', true)
            ->with('parent:id,name,slug')
            ->get(['id', 'name', 'name_en', 'slug', 'parent_id', 'depth', 'order']);

        return $this->buildHierarchicalList($categories);
    }

    private function buildHierarchicalList($categories, ?int $parentId = null, int $depth = 0): array
    {
        $result = [];
        $children = $categories->where('parent_id', $parentId)->sortBy('order')->values();

        foreach ($children as $child) {
            $child->depth = $depth;
            $result[] = $child;
            $subChildren = $this->buildHierarchicalList($categories, (int) $child->id, $depth + 1);
            foreach ($subChildren as $subChild) {
                $result[] = $subChild;
            }
        }

        return $result;
    }
}
