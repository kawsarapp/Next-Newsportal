<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    /**
     * Handle an incoming request and record audit activity when necessary.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log state-changing methods for authenticated users or important routes
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true) && auth()->check()) {
            try {
                $user = auth()->user();
                $portalId = $user->portal_id ?? (app()->bound('current.portal') ? app('current.portal')?->id : null);

                AuditLog::create([
                    'portal_id'    => $portalId,
                    'user_id'      => $user->id,
                    'action'       => $request->route()?->getName() ?: $request->path(),
                    'subject_type' => null,
                    'subject_id'   => null,
                    'old_values'   => null,
                    'new_values'   => null,
                    'context'      => [
                        'method' => $request->method(),
                        'url'    => $request->fullUrl(),
                        'input'  => $request->except(['password', 'password_confirmation', '_token']),
                        'status' => $response->getStatusCode(),
                    ],
                    'ip_address'   => $request->ip(),
                    'user_agent'   => substr((string) $request->userAgent(), 0, 500),
                ]);
            } catch (\Throwable $e) {
                // Fail silently so auditing never breaks standard user flows
                logger()->error('Audit log failed: ' . $e->getMessage());
            }
        }

        return $response;
    }
}
