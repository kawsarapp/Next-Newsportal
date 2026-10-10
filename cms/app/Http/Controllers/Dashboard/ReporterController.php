<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReporterController extends Controller
{
    public function index(): Response
    {
        $portalId = auth()->user()->portal_id;

        $reporters = User::withoutGlobalScopes()
            ->where(function ($q) use ($portalId) {
                if ($portalId) {
                    $q->where('portal_id', $portalId);
                }
            })
            ->withCount('posts')
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'reporter_type' => $u->reporter_type,
                'status' => $u->status ?? 'active',
                'posts_count' => $u->posts_count,
                'joined_date' => $u->created_at?->format('d M Y') ?? 'N/A',
            ]);

        return Inertia::render('Dashboard/Reporters/Index', [
            'reporters' => $reporters,
        ]);
    }

    public function show(User $reporter): Response|RedirectResponse
    {
        return redirect()->route('dashboard.reporters.index');
    }

    public function update(Request $request, User $reporter): RedirectResponse
    {
        $validated = $request->validate([
            'reporter_type' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $reporter->update($validated);

        return back()->with('success', 'রিপোর্টারের তথ্য সফলভাবে আপডেট করা হয়েছে!');
    }

    public function idCard(User $reporter): Response|RedirectResponse
    {
        return redirect()->route('dashboard.reporters.index')->with('info', 'রিপোর্টার আইডি কার্ড ফিচারটি প্রক্রিয়াধীন রয়েছে।');
    }
}
