<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

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

    public function invite(): Response
    {
        return Inertia::render('Dashboard/ComingSoon', ['title' => 'User Invite']);
    }

    public function sendInvite(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);
        return back()->with('success', 'আমন্ত্রণপত্র সফলভাবে পাঠানো হয়েছে!');
    }

    public function show($user): Response
    {
        return Inertia::render('Dashboard/ComingSoon', ['title' => 'User Profile']);
    }

    public function update(Request $request, $user): RedirectResponse
    {
        return back()->with('success', 'ব্যবহারকারীর তথ্য আপডেট করা হয়েছে!');
    }

    public function destroy($user): RedirectResponse
    {
        return back()->with('success', 'ব্যবহারকারী মুছে ফেলা হয়েছে!');
    }

    public function sessions($user): Response|JsonResponse
    {
        return Inertia::render('Dashboard/ComingSoon', ['title' => 'User Sessions']);
    }

    public function killSession($user, $session): RedirectResponse
    {
        return back()->with('success', 'সেশন বাতিল করা হয়েছে!');
    }
}
