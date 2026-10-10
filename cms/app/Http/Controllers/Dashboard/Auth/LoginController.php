<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Rate limit per IP
        $key = 'login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "অনেক বেশি চেষ্টা হয়েছে। {$seconds} সেকেন্ড পরে আবার চেষ্টা করুন।",
            ]);
        }

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($key, 60);

            // Log failed attempt
            LoginHistory::create([
                'user_id'        => \App\Models\User::where('email', $request->email)->value('id'),
                'ip_address'     => $request->ip(),
                'user_agent'     => $request->userAgent(),
                'status'         => 'failed',
                'failure_reason' => 'invalid_credentials',
            ]);

            throw ValidationException::withMessages([
                'email' => 'ইমেইল বা পাসওয়ার্ড সঠিক নয়।',
            ]);
        }

        RateLimiter::clear($key);

        $user = Auth::user();

        // Check if account is locked
        if ($user->isLocked()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => "আপনার অ্যাকাউন্ট সাময়িকভাবে লক। পরে চেষ্টা করুন।",
            ]);
        }

        // Check active status
        if ($user->status !== 'active') {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'আপনার অ্যাকাউন্ট নিষ্ক্রিয়।',
            ]);
        }

        // Log successful login
        LoginHistory::create([
            'user_id'    => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status'     => 'success',
        ]);

        $user->resetLoginAttempts();
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard.overview'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
