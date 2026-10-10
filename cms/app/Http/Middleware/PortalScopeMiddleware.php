<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Portal;
use App\Domain\Portal\ValueObjects\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * PortalScopeMiddleware
 *
 * Resolves the current portal from:
 *  1. X-Portal-Key header (frontend SPA — API key auth)
 *  2. Subdomain (dashboard — subdomain-based routing)
 *  3. Request domain (custom domain portals)
 *
 * Once resolved, binds 'current.portal' into the IoC container
 * so PortalScope (global scope) and all services can use it.
 */
class PortalScopeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $portal = $this->resolvePortal($request);

        if ($portal === null) {
            return response()->json([
                'success' => false,
                'message' => 'Portal not found or inactive.',
            ], 404);
        }

        if (! $portal->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'This portal is currently inactive.',
            ], 403);
        }

        // Bind into container — accessible everywhere via app('current.portal')
        app()->instance('current.portal', $portal);

        // Add portal info to response headers (useful for debugging)
        $response = $next($request);
        $response->headers->set('X-Portal-Id', (string) $portal->id);
        $response->headers->set('X-Portal-Slug', $portal->slug);

        return $response;
    }

    private function resolvePortal(Request $request): ?Portal
    {
        // Method 1: API Key in header (frontend → API)
        $apiKeyRaw = $request->header('X-Portal-Key');
        if ($apiKeyRaw) {
            return $this->findByApiKey($apiKeyRaw);
        }

        // Method 2: Domain-based (custom domains like clientnews.com)
        $host = $request->getHost();
        $portal = Portal::withoutGlobalScopes()
                        ->where('domain', $host)
                        ->where('status', 'active')
                        ->first();
        if ($portal) {
            return $portal;
        }

        // Method 3: Subdomain pattern (client.yourdomain.com)
        $subdomain = $this->extractSubdomain($host);
        if ($subdomain) {
            return Portal::withoutGlobalScopes()
                         ->where('slug', $subdomain)
                         ->where('status', 'active')
                         ->first();
        }

        return null;
    }

    private function findByApiKey(string $rawKey): ?Portal
    {
        // Extract prefix to narrow DB lookup (avoid full table scan)
        $prefix = substr($rawKey, 0, 12);

        $portal = Portal::withoutGlobalScopes()
                        ->where('api_key_prefix', $prefix)
                        ->first();

        if (! $portal) {
            return null;
        }

        // Verify against current key
        if (ApiKey::verify($rawKey, $portal->api_key_hash)) {
            return $portal;
        }

        // Verify against old key (30-day grace period after rotation)
        if ($portal->api_key_old_hash && ApiKey::verify($rawKey, $portal->api_key_old_hash)) {
            // Warn — old key still in use
            logger()->warning('Old API key in use', ['portal_id' => $portal->id]);
            return $portal;
        }

        return null;
    }

    private function extractSubdomain(string $host): ?string
    {
        $appDomain = parse_url(config('app.url'), PHP_URL_HOST);
        if ($appDomain && str_ends_with($host, '.' . $appDomain)) {
            $sub = str_replace('.' . $appDomain, '', $host);
            return $sub !== $appDomain ? $sub : null;
        }
        return null;
    }
}
