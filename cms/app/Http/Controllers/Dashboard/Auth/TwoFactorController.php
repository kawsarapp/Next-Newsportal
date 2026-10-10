<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class TwoFactorController extends Controller
{
    public function show()   { return Inertia::render('Auth/TwoFactor'); }
    public function verify() { return redirect()->route('dashboard.overview'); }
}
