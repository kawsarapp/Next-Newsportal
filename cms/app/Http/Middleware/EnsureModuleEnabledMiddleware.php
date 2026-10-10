<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabledMiddleware
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $portal = app()->bound('current.portal') ? app('current.portal') : null;

        if ($portal && !$portal->moduleEnabled($module)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "The module [{$module}] is not enabled for this portal.",
                ], 403);
            }

            abort(403, "The module [{$module}] is not enabled for this portal.");
        }

        return $next($request);
    }
}
