<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user   = $request->user();
        $portal = $user?->portal;

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'email'       => $user->email,
                    'avatar'      => $user->avatar,
                    'roles'       => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                    'portal_id'   => $user->portal_id,
                ] : null,
            ],
            'portal' => $portal ? [
                'id'   => $portal->id,
                'name' => $portal->name,
                'slug' => $portal->slug,
                'logo' => $portal->logo_path,
            ] : null,
            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
                'info'    => $request->session()->get('info'),
            ],
        ];
    }
}
