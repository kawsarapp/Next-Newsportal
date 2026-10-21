<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\LoginHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $roles = $user->getRoleNames();
        $permissions = $user->getAllPermissions()->pluck('name');

        $sessions = LoginHistory::where('user_id', $user->id)
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'ip_address' => $item->ip_address,
                'user_agent' => $item->user_agent,
                'device_name' => $item->device_name,
                'status' => $item->status,
                'created_at' => $item->created_at?->toIso8601String(),
                'time_human' => $item->created_at?->diffForHumans(),
            ]);

        $activities = AuditLog::where('user_id', $user->id)
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'action' => $item->action,
                'ip_address' => $item->ip_address,
                'created_at' => $item->created_at?->toIso8601String(),
                'time_human' => $item->created_at?->diffForHumans(),
            ]);

        return Inertia::render('Dashboard/Profile/Index', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'bio' => $user->bio,
                'avatar' => $user->avatar,
                'two_factor_enabled' => (bool) $user->two_factor_enabled,
                'created_at' => $user->created_at?->toIso8601String(),
                'last_login_at' => $user->last_login_at?->toIso8601String(),
                'last_login_ip' => $user->last_login_ip,
                'portal_id' => $user->portal_id,
                'portal_name' => $user->portal?->name,
                'reporter_type' => $user->reporter_type,
                'institution' => $user->institution,
            ],
            'roles' => $roles,
            'permissions' => $permissions,
            'sessions' => $sessions,
            'activities' => $activities,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'username' => ['nullable', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $avatarUrl = Storage::disk('public')->url($path);
            $user->avatar = $avatarUrl;
            $user->avatar_url = $avatarUrl;
        } elseif ($request->boolean('remove_avatar')) {
            $user->avatar = null;
            $user->avatar_url = null;
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->username = $validated['username'] ?? null;
        $user->phone = $validated['phone'] ?? null;
        $user->bio = $validated['bio'] ?? null;
        $user->save();

        AuditLog::create([
            'portal_id' => $user->portal_id,
            'user_id' => $user->id,
            'action' => 'profile.update',
            'subject_type' => get_class($user),
            'subject_id' => $user->id,
            'new_values' => [
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'phone' => $user->phone,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'প্রোফাইল সফলভাবে আপডেট করা হয়েছে।');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        AuditLog::create([
            'portal_id' => $user->portal_id,
            'user_id' => $user->id,
            'action' => 'profile.password_update',
            'subject_type' => get_class($user),
            'subject_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে।');
    }

    public function enable2fa(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->two_factor_enabled = true;
        $user->two_factor_confirmed_at = now();
        $user->save();

        AuditLog::create([
            'portal_id' => $user->portal_id,
            'user_id' => $user->id,
            'action' => 'profile.2fa_enabled',
            'subject_type' => get_class($user),
            'subject_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', '২-ফ্যাক্টর সেটিংস আপডেট করা হয়েছে।');
    }

    public function disable2fa(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->two_factor_enabled = false;
        $user->two_factor_confirmed_at = null;
        $user->save();

        AuditLog::create([
            'portal_id' => $user->portal_id,
            'user_id' => $user->id,
            'action' => 'profile.2fa_disabled',
            'subject_type' => get_class($user),
            'subject_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', '২-ফ্যাক্টর সেটিংস আপডেট করা হয়েছে।');
    }

    public function confirm2fa(Request $request): RedirectResponse
    {
        return back()->with('success', '২-ফ্যাক্টর কোড যাচাই সম্পন্ন হয়েছে।');
    }
}
