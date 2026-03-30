<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Str;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        $deviceId = $request->cookie('pkl_device_id');

    if (!$deviceId) {
        $deviceId = (string) Str::uuid();
        cookie()->queue('pkl_device_id', $deviceId, 60 * 24 * 365); // 1 tahun
    }

    if ($user->role === 'peserta') {

        if ($user->device_hash && $user->device_hash !== hash('sha256', $deviceId)) {
          Auth::guard('web')->logout();
            return back()->withErrors([
                'email' => 'Akun ini sudah terdaftar di perangkat lain.',
            ]);
        }

        if (!$user->device_hash) {
            $user->update([
                'device_hash' => hash('sha256', $deviceId),
                'last_login_ip' => $request->ip(),
                'last_login_user_agent' => $request->userAgent(),
                'last_login_at' => now(),
            ]);
        }
    }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
