<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FacebookPublisherService
{
    private const GRAPH_VERSION = 'v19.0';
    private const BASE_URL = 'https://graph.facebook.com/' . self::GRAPH_VERSION;

    /**
     * Publish photo with first comment to a Facebook Page.
     *
     * @param string $pageAccessToken
     * @param string $pageId
     * @param string $imagePathOrUrl Local path, storage path, or public URL
     * @param string $caption
     * @param string $commentText
     * @return array{
     *     success: bool,
     *     is_simulation: bool,
     *     post_id: ?string,
     *     comment_id: ?string,
     *     post_url: ?string,
     *     message: string,
     *     error?: string
     * }
     */
    public function publishPhotoWithFirstComment(
        string $pageAccessToken,
        string $pageId,
        string $imagePathOrUrl,
        string $caption,
        string $commentText
    ): array {
        $pageAccessToken = trim($pageAccessToken);
        $pageId = trim($pageId);

        // Check if credentials are missing or placeholder/simulation
        if (
            empty($pageAccessToken) ||
            empty($pageId) ||
            str_starts_with($pageAccessToken, 'token_') ||
            str_starts_with($pageAccessToken, 'mock_') ||
            $pageAccessToken === 'your_facebook_page_access_token' ||
            str_starts_with($pageId, 'fb-news-page')
        ) {
            Log::info('[FacebookPublisher] Simulation Mode active - dummy or missing token.', [
                'page_id' => $pageId,
                'caption_len' => strlen($caption),
            ]);

            return [
                'success' => true,
                'is_simulation' => true,
                'post_id' => 'sim_fb_post_' . uniqid(),
                'comment_id' => 'sim_fb_cmt_' . uniqid(),
                'post_url' => 'https://facebook.com',
                'message' => 'সিমুলেশন মোড: ফেসবুক পেজ আইডি বা লাইভ অ্যাক্সেস টোকেন কনফিগার করা হয়নি। ড্যাশবোর্ডে টোকেন সেট করলে সরাসরি ফেসবুক পেজে পোস্ট ও প্রথম কমেন্টে লিংক যুক্ত হবে।',
            ];
        }

        try {
            // Step 1: Upload Photo with Caption
            $photoEndpoint = self::BASE_URL . "/{$pageId}/photos";
            $photoPayload = [
                'caption' => $caption,
                'access_token' => $pageAccessToken,
            ];

            // Resolve file if local
            $resolvedFile = $this->resolveLocalFile($imagePathOrUrl);

            if ($resolvedFile && file_exists($resolvedFile)) {
                $response = Http::timeout(35)
                    ->attach('source', file_get_contents($resolvedFile), basename($resolvedFile))
                    ->post($photoEndpoint, $photoPayload);
            } else {
                // Use URL
                $photoPayload['url'] = $imagePathOrUrl;
                $response = Http::timeout(35)->post($photoEndpoint, $photoPayload);
            }

            if (!$response->successful()) {
                $fbError = $response->json('error.message') ?? $response->body();
                Log::warning('[FacebookPublisher] Photo upload failed: ' . $fbError, [
                    'page_id' => $pageId,
                    'status' => $response->status(),
                ]);

                return [
                    'success' => false,
                    'is_simulation' => false,
                    'post_id' => null,
                    'comment_id' => null,
                    'post_url' => null,
                    'error' => $fbError,
                    'message' => 'ফেসবুক এপিআই এরর: ' . $fbError,
                ];
            }

            $photoData = $response->json();
            $photoId = (string) ($photoData['id'] ?? '');
            $postId = (string) ($photoData['post_id'] ?? $photoId);

            // Step 2: Post the First Comment with the Article Link
            $commentId = null;
            if (!empty($postId) && !empty(trim($commentText))) {
                $commentEndpoint = self::BASE_URL . "/{$postId}/comments";
                $commentResponse = Http::timeout(25)->post($commentEndpoint, [
                    'message' => $commentText,
                    'access_token' => $pageAccessToken,
                ]);

                if ($commentResponse->successful()) {
                    $commentId = (string) ($commentResponse->json('id') ?? '');
                } else {
                    Log::warning('[FacebookPublisher] Comment creation failed: ' . $commentResponse->body());
                }
            }

            $facebookPostUrl = "https://facebook.com/{$postId}";

            return [
                'success' => true,
                'is_simulation' => false,
                'post_id' => $postId,
                'comment_id' => $commentId,
                'post_url' => $facebookPostUrl,
                'message' => 'ফেসবুকে সফলভাবে ফটাকার্ড পোস্ট এবং প্রথম কমেন্টে সংবাদের লিংক পাবলিশ হয়েছে!',
            ];
        } catch (Throwable $e) {
            Log::error('[FacebookPublisher] Exception occurred: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'is_simulation' => false,
                'post_id' => null,
                'comment_id' => null,
                'post_url' => null,
                'error' => $e->getMessage(),
                'message' => 'ফেসবুকে প্রকাশ করার সময় সমস্যা হয়েছে: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Test connection to Facebook Graph API for verification.
     */
    public function testConnection(string $pageAccessToken, string $pageId): array
    {
        $pageAccessToken = trim($pageAccessToken);
        $pageId = trim($pageId);

        if (empty($pageAccessToken) || empty($pageId)) {
            return [
                'success' => false,
                'connected' => false,
                'message' => 'ফেসবুক পেজ আইডি এবং অ্যাক্সেস টোকেন উভয়ই প্রদান করুন।',
            ];
        }

        if (str_starts_with($pageAccessToken, 'token_') || str_starts_with($pageAccessToken, 'mock_') || str_starts_with($pageId, 'fb-news-page')) {
            return [
                'success' => true,
                'connected' => true,
                'is_simulation' => true,
                'name' => 'সিমুলেশন পেজ (ডেমো মোড)',
                'id' => $pageId,
                'message' => 'সিমুলেশন মোড সক্রিয়। লাইভ মেটা অ্যাক্সেস টোকেন দিলে বাস্তব ফেসবুক পেজের সাথে সিঙ্ক হবে।',
            ];
        }

        try {
            $response = Http::timeout(15)->get(self::BASE_URL . "/{$pageId}", [
                'fields' => 'id,name,link,is_published,fan_count',
                'access_token' => $pageAccessToken,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'connected' => true,
                    'is_simulation' => false,
                    'name' => $data['name'] ?? 'Facebook Page',
                    'id' => $data['id'] ?? $pageId,
                    'link' => $data['link'] ?? null,
                    'fan_count' => $data['fan_count'] ?? null,
                    'is_published' => $data['is_published'] ?? true,
                    'message' => "ফেসবুক পেজ '{$data['name']}' সফলভাবে সংযুক্ত হয়েছে!",
                ];
            }

            $errMsg = $response->json('error.message') ?? 'ফেসবুক পেজের সাথে যোগাযোগ করা যায়নি।';
            return [
                'success' => false,
                'connected' => false,
                'message' => 'কানেকশন ব্যর্থ: ' . $errMsg,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'connected' => false,
                'message' => 'নেটওয়ার্ক বা সিস্টেম এরর: ' . $e->getMessage(),
            ];
        }
    }

    private function resolveLocalFile(string $pathOrUrl): ?string
    {
        if (file_exists($pathOrUrl)) {
            return $pathOrUrl;
        }

        // Check public storage
        $clean = ltrim(parse_url($pathOrUrl, PHP_URL_PATH) ?? '', '/');
        if (str_starts_with($clean, 'storage/')) {
            $storageRel = substr($clean, 8);
            if (Storage::disk('public')->exists($storageRel)) {
                return Storage::disk('public')->path($storageRel);
            }
        }

        if (file_exists(public_path($clean))) {
            return public_path($clean);
        }

        return null;
    }
}
