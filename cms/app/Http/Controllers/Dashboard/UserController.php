<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $portalId = auth()->user()->portal_id;

        $users = User::withoutGlobalScopes()
            ->where(function ($q) use ($portalId) {
                if ($portalId) {
                    $q->where('portal_id', $portalId);
                }
            })
            ->with('roles')
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->roles->first()?->name ?? 'Reporter',
            ]);

        return Inertia::render('Dashboard/Users/Index', [
            'users' => $users,
        ]);
    }
}
