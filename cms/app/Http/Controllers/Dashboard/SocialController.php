<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SocialController extends Controller
{
    public function index(): Response
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $accounts = SocialAccount::where('portal_id', $portalId)->get();

        $history = SocialPost::where('portal_id', $portalId)
            ->with(['socialAccount:id,account_name,platform', 'post:id,title,slug'])
            ->latest()
            ->limit(20)
            ->get();

        $recentArticles = Post::withoutGlobalScopes()
            ->where('portal_id', $portalId)
            ->where('status', 'published')
            ->latest('published_at')
            ->limit(20)
            ->get(['id', 'title', 'slug', 'published_at']);

        return Inertia::render('Dashboard/Social/Index', [
            'accounts'        => $accounts,
            'history'         => $history,
            'recent_articles' => $recentArticles,
        ]);
    }

    public function accounts(): Response
    {
        return $this->index();
    }

    public function queue(): Response
    {
        return $this->index();
    }

    public function storeAccount(Request $request): RedirectResponse
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $validated = $request->validate([
            'platform'     => ['required', 'string', 'in:facebook,telegram,x,youtube,instagram'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_id'   => ['nullable', 'string', 'max:255'],
            'access_token' => ['nullable', 'string'],
        ]);

        $accountId = !empty($validated['account_id'])
            ? trim($validated['account_id'])
            : ('@' . Str::slug($validated['account_name']) . '_' . Str::random(4));

        $token = !empty($validated['access_token'])
            ? $validated['access_token']
            : ('token_' . Str::random(32));

        SocialAccount::create([
            'portal_id'    => $portalId,
            'platform'     => $validated['platform'],
            'account_name' => $validated['account_name'],
            'account_id'   => $accountId,
            'access_token' => encrypt($token),
            'status'       => 'active',
            'meta'         => [
                'added_by' => auth()->id(),
                'platform' => $validated['platform'],
            ],
        ]);

        return back()->with('success', 'নতুন সোশ্যাল চ্যানেল সফলভাবে যুক্ত করা হয়েছে!');
    }

    public function destroyAccount(SocialAccount $account): RedirectResponse
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        if ($account->portal_id !== $portalId) {
            abort(403, 'Unauthorized portal action.');
        }

        $account->delete();

        return back()->with('success', 'সোশ্যাল চ্যানেল মুছে ফেলা হয়েছে!');
    }

    public function publish(Request $request): RedirectResponse
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $validated = $request->validate([
            'account_id'   => ['nullable', 'integer', 'exists:social_accounts,id'],
            'account_ids'  => ['nullable', 'array'],
            'account_ids.*'=> ['integer', 'exists:social_accounts,id'],
            'caption'      => ['nullable', 'string'],
            'message'      => ['nullable', 'string'],
            'post_id'      => ['nullable', 'integer', 'exists:posts,id'],
            'news_url'     => ['nullable', 'string', 'max:500'],
        ]);

        $caption = trim((string)($validated['caption'] ?? $validated['message'] ?? ''));
        if (empty($caption)) {
            return back()->withErrors(['caption' => 'ক্যাপশন বা বার্তা আবশ্যক।']);
        }

        $targetAccountIds = !empty($validated['account_ids'])
            ? $validated['account_ids']
            : (!empty($validated['account_id']) ? [$validated['account_id']] : []);

        if (empty($targetAccountIds)) {
            $targetAccountIds = SocialAccount::where('portal_id', $portalId)
                ->where('status', 'active')
                ->pluck('id')
                ->toArray();
        }

        if (empty($targetAccountIds)) {
            return back()->withErrors(['account_id' => 'কোনো সক্রিয় সোশ্যাল মিডিয়া চ্যানেল পাওয়া যায়নি।']);
        }

        $accounts = SocialAccount::where('portal_id', $portalId)
            ->whereIn('id', $targetAccountIds)
            ->get();

        $publishedCount = 0;
        foreach ($accounts as $acc) {
            SocialPost::create([
                'portal_id'         => $portalId,
                'post_id'           => $validated['post_id'] ?? null,
                'social_account_id' => $acc->id,
                'platform'          => $acc->platform,
                'caption'           => $caption . (!empty($validated['news_url']) ? "\n" . $validated['news_url'] : ''),
                'status'            => 'published',
                'published_at'      => now(),
                'platform_post_id'  => strtolower($acc->platform) . '_' . uniqid(),
                'performance'       => [
                    'likes'    => 0,
                    'shares'   => 0,
                    'comments' => 0,
                    'reach'    => 0,
                ],
            ]);
            $publishedCount++;
        }

        return back()->with('success', "{$publishedCount}টি সোশ্যাল চ্যানেলে সফলভাবে পোস্ট প্রকাশিত হয়েছে!");
    }
}
