<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalActiveMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $portal = app()->bound('current.portal') ? app('current.portal') : null;

        if ($portal && !$portal->isActive()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This portal is inactive or suspended.',
                ], 403);
            }

            abort(403, 'This portal is inactive or suspended.');
        }

        return $next($request);
    }
}
