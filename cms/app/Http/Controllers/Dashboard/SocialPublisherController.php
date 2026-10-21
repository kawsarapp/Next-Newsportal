<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Domain\Post\ValueObjects\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\District;
use App\Models\Division;
use App\Models\Portal;
use App\Models\PortalSetting;
use App\Models\Post;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Services\FacebookPublisherService;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class SocialPublisherController extends Controller
{
    public function __construct(
        private readonly PostService $postService,
        private readonly FacebookPublisherService $fbService
    ) {}

    public function index(Request $request): Response
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;
        $portal = Portal::find($portalId) ?? Portal::first();

        // 1. Categories
        $categories = Category::where('portal_id', $portalId)
            ->where('is_active', true)
            ->with('parent:id,name,slug')
            ->orderBy('order')
            ->get(['id', 'name', 'name_en', 'slug', 'parent_id', 'depth', 'order']);

        // Hierarchical tree list
        $categoryTree = $this->buildHierarchicalList($categories);

        // 2. Divisions & Districts
        $divisions = Division::with(['districts' => fn ($q) => $q->orderBy('id')])
            ->orderBy('id')
            ->get(['id', 'name', 'bn_name']);

        // 3. Facebook Connection & Settings
        $fbAccount = SocialAccount::where('portal_id', $portalId)
            ->where('platform', 'facebook')
            ->first();

        $savedToken = null;
        if ($fbAccount && !empty($fbAccount->access_token)) {
            try {
                $savedToken = decrypt($fbAccount->access_token);
            } catch (Throwable) {
                $savedToken = $fbAccount->access_token;
            }
        }

        if (empty($savedToken)) {
            $savedToken = PortalSetting::where('portal_id', $portalId)
                ->where('key', 'facebook_page_access_token')
                ->value('value');
        }

        $fbPageId = $fbAccount?->account_id
            ?? PortalSetting::where('portal_id', $portalId)->where('key', 'facebook_page_id')->value('value')
            ?? '';

        $facebookSettings = [
            'page_id'      => $fbPageId,
            'page_name'    => $fbAccount?->account_name ?? 'অফিসিয়াল ফেসবুক পেজ',
            'has_token'    => !empty($savedToken),
            'status'       => $fbAccount?->status ?? 'active',
            'is_connected' => !empty($savedToken) && !empty($fbPageId),
            'meta'         => $fbAccount?->meta ?? [],
        ];

        // 4. Publish History (Photocards & Facebook posts)
        $history = Post::withoutGlobalScopes()
            ->where('portal_id', $portalId)
            ->whereNotNull('featured_image')
            ->with([
                'category:id,name,slug',
                'author:id,name',
            ])
            ->latest('id')
            ->paginate(15)
            ->through(function ($post) use ($portalId) {
                $socialPost = SocialPost::where('portal_id', $portalId)
                    ->where('post_id', $post->id)
                    ->where('platform', 'facebook')
                    ->latest()
                    ->first();

                return [
                    'id'             => $post->id,
                    'title'          => $post->title,
                    'slug'           => $post->slug,
                    'excerpt'        => $post->excerpt,
                    'body'           => $post->body,
                    'category_id'    => $post->category_id,
                    'featured_image' => $post->featured_image,
                    'category'       => $post->category?->name,
                    'author'         => $post->author?->name ?? 'সম্পাদক',
                    'status'         => $post->status?->value ?? $post->status,
                    'published_at'   => $post->published_at?->format('d M Y, h:i A') ?? $post->created_at->format('d M Y, h:i A'),
                    'news_url'       => $this->buildNewsUrl($post),
                    'facebook_post'  => $socialPost ? [
                        'id'               => $socialPost->id,
                        'status'           => $socialPost->status,
                        'platform_post_id' => $socialPost->platform_post_id,
                        'post_url'         => $socialPost->platform_post_id && !str_starts_with($socialPost->platform_post_id, 'sim_')
                            ? "https://facebook.com/{$socialPost->platform_post_id}"
                            : null,
                        'has_first_comment'=> !empty($socialPost->performance['first_comment_id']) || ($socialPost->performance['comments'] ?? 0) > 0,
                        'is_simulation'    => (bool)($socialPost->performance['is_simulation'] ?? false),
                        'error_message'    => $socialPost->error_message,
                        'published_at'     => $socialPost->published_at?->format('d M Y, h:i A'),
                    ] : null,
                ];
            });

        // 5. Aggregate stats
        $totalPhotocards = Post::withoutGlobalScopes()
            ->where('portal_id', $portalId)
            ->whereNotNull('featured_image')
            ->count();

        $fbPublishedCount = SocialPost::where('portal_id', $portalId)
            ->where('platform', 'facebook')
            ->where('status', 'published')
            ->count();

        return Inertia::render('Dashboard/SocialPublisher/Index', [
            'categories'        => $categoryTree,
            'divisions'         => $divisions,
            'history'           => $history,
            'facebookSettings'  => $facebookSettings,
            'portal'            => [
                'name'      => $portal?->name ?? 'News Portal',
                'tagline'   => $portal?->tagline ?? 'সত্যের সাথে প্রতিদিন',
                'domain'    => $portal?->domain,
                'logo_path' => $portal?->logo_path,
            ],
            'stats'             => [
                'total_photocards' => $totalPhotocards,
                'fb_published'     => $fbPublishedCount,
                'today_count'      => Post::withoutGlobalScopes()
                    ->where('portal_id', $portalId)
                    ->whereDate('created_at', today())
                    ->count(),
            ],
        ]);
    }

    /**
     * Dual Instant Publishing (Website + Facebook with first comment)
     */
    public function publish(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title'                  => ['required', 'string', 'max:255'],
            'category_id'            => ['required', 'integer', 'exists:categories,id'],
            'secondary_category_ids' => ['nullable', 'array'],
            'secondary_category_ids.*'=> ['integer', 'exists:categories,id'],
            'division_id'            => ['nullable', 'integer', 'exists:divisions,id'],
            'district_id'            => ['nullable', 'integer', 'exists:districts,id'],
            'excerpt'                => ['nullable', 'string', 'max:1000'],
            'body'                   => ['nullable', 'string'],
            'photocard_image'        => ['required', 'string'], // base64 data url or public url
            'post_to_facebook'       => ['nullable', 'boolean'],
            'custom_caption'         => ['nullable', 'string', 'max:2000'],
            'is_breaking'            => ['nullable', 'boolean'],
            'is_featured'            => ['nullable', 'boolean'],
        ]);

        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;
        $authorId = auth()->id() ?? 1;

        // 1. Process and save Photocard image
        $imageInfo = $this->storePhotocardImage($validated['photocard_image']);
        if (!$imageInfo) {
            return back()->withErrors(['photocard_image' => 'ফটাকার্ড ছবি প্রক্রিয়া করতে ব্যর্থ হয়েছে।']);
        }

        // 2. Prepare Post payload
        $bodyText = !empty($validated['body']) ? $validated['body'] : ($validated['excerpt'] ?? $validated['title']);
        $excerptText = !empty($validated['excerpt']) ? $validated['excerpt'] : Str::limit(strip_tags($bodyText), 160);

        $postData = [
            'portal_id'              => $portalId,
            'title'                  => $validated['title'],
            'excerpt'                => $excerptText,
            'body'                   => $bodyText,
            'category_id'            => $validated['category_id'],
            'secondary_category_ids' => $validated['secondary_category_ids'] ?? [],
            'division_id'            => $validated['division_id'] ?? null,
            'district_id'            => $validated['district_id'] ?? null,
            'is_breaking'            => (bool)($validated['is_breaking'] ?? false),
            'is_featured'            => (bool)($validated['is_featured'] ?? false),
            'type'                   => 'article',
            'featured_image'         => [
                'original'     => $imageInfo['url'],
                'sm'           => $imageInfo['url'],
                'caption'      => $validated['title'],
                'alt'          => $validated['title'],
                'is_photocard' => true,
            ],
            'seo_title'              => Str::limit($validated['title'], 70),
            'seo_description'        => Str::limit($excerptText, 160),
            'published_at'           => now(),
        ];

        // 3. Create and publish Post
        $post = $this->postService->create($postData, $authorId);
        $this->postService->changeStatus($post, PostStatus::Published, $authorId);

        // 4. Generate Website Live URL
        $newsUrl = $this->buildNewsUrl($post);

        // 5. Facebook Publishing (if enabled)
        $fbNotice = null;
        if (!empty($validated['post_to_facebook'])) {
            $fbAccount = SocialAccount::where('portal_id', $portalId)
                ->where('platform', 'facebook')
                ->first();

            $token = null;
            if ($fbAccount && !empty($fbAccount->access_token)) {
                try {
                    $token = decrypt($fbAccount->access_token);
                } catch (Throwable) {
                    $token = $fbAccount->access_token;
                }
            }
            if (empty($token)) {
                $token = PortalSetting::where('portal_id', $portalId)
                    ->where('key', 'facebook_page_access_token')
                    ->value('value') ?? '';
            }

            $pageId = $fbAccount?->account_id
                ?? PortalSetting::where('portal_id', $portalId)->where('key', 'facebook_page_id')->value('value')
                ?? 'fb-news-page-1';

            $caption = !empty($validated['custom_caption'])
                ? trim($validated['custom_caption'])
                : ($post->title . "\n\n" . $excerptText);

            $commentText = "বিস্তারিত পড়তে ক্লিক করুন: {$newsUrl}";

            $fbResult = $this->fbService->publishPhotoWithFirstComment(
                $token,
                $pageId,
                $imageInfo['path'] ?: $imageInfo['url'],
                $caption,
                $commentText
            );

            // Record SocialPost
            SocialPost::create([
                'portal_id'         => $portalId,
                'post_id'           => $post->id,
                'social_account_id' => $fbAccount?->id,
                'platform'          => 'facebook',
                'caption'           => $caption . "\n[১ম কমেন্ট]: " . $commentText,
                'status'            => $fbResult['success'] ? 'published' : 'failed',
                'published_at'      => $fbResult['success'] ? now() : null,
                'platform_post_id'  => $fbResult['post_id'] ?? null,
                'error_message'     => $fbResult['error'] ?? null,
                'performance'       => [
                    'likes'            => 0,
                    'shares'           => 0,
                    'comments'         => $fbResult['comment_id'] ? 1 : 0,
                    'reach'            => 0,
                    'first_comment_id' => $fbResult['comment_id'] ?? null,
                    'is_simulation'    => (bool)($fbResult['is_simulation'] ?? false),
                ],
            ]);

            $fbNotice = $fbResult['message'];
        }

        $message = "সংবাদটি ওয়েবসাইটে সফলভাবে লাইভ প্রকাশিত হয়েছে!";
        if ($fbNotice) {
            $message .= " (" . $fbNotice . ")";
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => $message,
                'post_id'  => $post->id,
                'news_url' => $newsUrl,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Re-share an existing post to Facebook
     */
    public function reShare(Request $request, Post $post): RedirectResponse|JsonResponse
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $fbAccount = SocialAccount::where('portal_id', $portalId)
            ->where('platform', 'facebook')
            ->first();

        $token = null;
        if ($fbAccount && !empty($fbAccount->access_token)) {
            try {
                $token = decrypt($fbAccount->access_token);
            } catch (Throwable) {
                $token = $fbAccount->access_token;
            }
        }
        if (empty($token)) {
            $token = PortalSetting::where('portal_id', $portalId)
                ->where('key', 'facebook_page_access_token')
                ->value('value') ?? '';
        }

        $pageId = $fbAccount?->account_id
            ?? PortalSetting::where('portal_id', $portalId)->where('key', 'facebook_page_id')->value('value')
            ?? 'fb-news-page-1';

        $newsUrl = $this->buildNewsUrl($post);
        $imageUrl = $post->featured_image['original'] ?? $post->featured_image['sm'] ?? null;

        if (!$imageUrl) {
            return back()->withErrors(['general' => 'এই সংবাদের কোনো ফটাকার্ড বা ছবি পাওয়া যায়নি।']);
        }

        $caption = $post->title . "\n\n" . ($post->excerpt ?: Str::limit(strip_tags($post->body ?? ''), 160));
        $commentText = "বিস্তারিত পড়তে ক্লিক করুন: {$newsUrl}";

        $fbResult = $this->fbService->publishPhotoWithFirstComment(
            $token,
            $pageId,
            $imageUrl,
            $caption,
            $commentText
        );

        SocialPost::create([
            'portal_id'         => $portalId,
            'post_id'           => $post->id,
            'social_account_id' => $fbAccount?->id,
            'platform'          => 'facebook',
            'caption'           => $caption . "\n[১ম কমেন্ট]: " . $commentText,
            'status'            => $fbResult['success'] ? 'published' : 'failed',
            'published_at'      => $fbResult['success'] ? now() : null,
            'platform_post_id'  => $fbResult['post_id'] ?? null,
            'error_message'     => $fbResult['error'] ?? null,
            'performance'       => [
                'likes'            => 0,
                'shares'           => 0,
                'comments'         => $fbResult['comment_id'] ? 1 : 0,
                'reach'            => 0,
                'first_comment_id' => $fbResult['comment_id'] ?? null,
                'is_simulation'    => (bool)($fbResult['is_simulation'] ?? false),
            ],
        ]);

        return back()->with('success', 'ফেসবুকে পুনরায় শেয়ার সফল হয়েছে: ' . $fbResult['message']);
    }

    /**
     * Save Facebook Page credentials
     */
    public function saveFacebookSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'page_id'      => ['required', 'string', 'max:255'],
            'page_name'    => ['nullable', 'string', 'max:255'],
            'access_token' => ['nullable', 'string'],
        ]);

        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        // 1. Update/Create SocialAccount
        $account = SocialAccount::firstOrNew([
            'portal_id' => $portalId,
            'platform'  => 'facebook',
        ]);

        $account->account_id = trim($validated['page_id']);
        $account->account_name = !empty($validated['page_name']) ? trim($validated['page_name']) : ($account->account_name ?: 'ফেসবুক পেজ');
        $account->status = 'active';

        if (!empty($validated['access_token'])) {
            $account->access_token = encrypt(trim($validated['access_token']));

            // Also keep in PortalSetting for cross-compatibility
            PortalSetting::updateOrCreate(
                ['portal_id' => $portalId, 'key' => 'facebook_page_access_token'],
                ['value' => trim($validated['access_token']), 'type' => 'string', 'group' => 'social']
            );
        }

        $account->meta = array_merge($account->meta ?? [], [
            'updated_by' => auth()->id(),
            'updated_at' => now()->toIso8601String(),
        ]);
        $account->save();

        PortalSetting::updateOrCreate(
            ['portal_id' => $portalId, 'key' => 'facebook_page_id'],
            ['value' => trim($validated['page_id']), 'type' => 'string', 'group' => 'social']
        );

        return back()->with('success', 'ফেসবুক পেজ সেটিংস সফলভাবে সংরক্ষিত হয়েছে!');
    }

    /**
     * Test connection to Facebook Graph API
     */
    public function testFacebookConnection(Request $request): JsonResponse
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $pageId = $request->input('page_id');
        $token = $request->input('access_token');

        if (empty($token)) {
            $fbAccount = SocialAccount::where('portal_id', $portalId)
                ->where('platform', 'facebook')
                ->first();

            if ($fbAccount && !empty($fbAccount->access_token)) {
                try {
                    $token = decrypt($fbAccount->access_token);
                } catch (Throwable) {
                    $token = $fbAccount->access_token;
                }
            }
        }

        if (empty($pageId)) {
            $pageId = SocialAccount::where('portal_id', $portalId)
                ->where('platform', 'facebook')
                ->value('account_id');
        }

        $result = $this->fbService->testConnection((string) $token, (string) $pageId);

        return response()->json($result);
    }

    /**
     * Decode base64 image data and store to public disk
     */
    private function storePhotocardImage(string $imageData): ?array
    {
        try {
            if (str_starts_with($imageData, 'data:image')) {
                // e.g. "data:image/png;base64,iVBORw0KGgoAAA..."
                [$header, $data] = explode(';', $imageData);
                [, $data]        = explode(',', $data);
                $decoded         = base64_decode($data);

                $filename = 'photocard_' . date('Ymd_His') . '_' . Str::random(8) . '.png';
                $relPath  = 'uploads/photocards/' . $filename;

                Storage::disk('public')->put($relPath, $decoded);

                return [
                    'filename' => $filename,
                    'path'     => Storage::disk('public')->path($relPath),
                    'url'      => asset('storage/' . $relPath),
                ];
            }

            // Already a URL or relative path
            return [
                'filename' => basename($imageData),
                'path'     => null,
                'url'      => $imageData,
            ];
        } catch (Throwable $e) {
            Log::error('[SocialPublisher] Failed to store photocard image: ' . $e->getMessage());
            return null;
        }
    }

    private function buildNewsUrl(Post $post): string
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;
        $portal = Portal::find($portalId) ?? Portal::first();

        $domain = $portal?->domain;
        if (!empty($domain)) {
            $base = str_starts_with($domain, 'http') ? $domain : 'https://' . $domain;
        } else {
            $base = config('app.url', url('/'));
        }

        return rtrim($base, '/') . '/news/' . $post->slug;
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
