<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * PortalScope — Automatically scopes ALL queries to the current portal.
 *
 * This is the critical security boundary in the multi-tenant system.
 * Every model that has portal_id MUST use this scope.
 * Even if a developer forgets to filter by portal, this scope protects the data.
 */
class PortalScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $portalId = $this->resolvePortalId();

        if ($portalId !== null) {
            $builder->where($model->getTable() . '.portal_id', $portalId);
        }
    }

    private function resolvePortalId(): ?int
    {
        // 1. Injected by PortalScope middleware (API requests)
        if (app()->bound('current.portal')) {
            return app('current.portal')?->id;
        }

        // 2. From authenticated user's portal (dashboard requests)
        if (auth()->check() && auth()->user()->portal_id) {
            return auth()->user()->portal_id;
        }

        // 3. Super admin — no scope (sees all portals)
        // This is intentional — super admin bypasses scope
        return null;
    }
}
