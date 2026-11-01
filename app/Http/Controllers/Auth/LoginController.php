<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RegistrationSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /** M01: halaman login. Tombol Daftar hanya muncul saat pendaftaran dibuka (PB-30, M01f). */
    public function show(): View
    {
        return view('auth.login', ['registrationOpen' => RegistrationSetting::isOpen()]);
    }

    /** FR-01: masuk dengan NISN/username + kata sandi. */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'NISN atau username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // Batasi 5x salah per akun + IP (satu sekolah bisa pakai IP yang sama).
        $key = Str::lower($credentials['username']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        // M01b: data salah.
        if (! Auth::attempt($credentials)) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'username' => 'NISN/username atau kata sandi salah.',
            ]);
        }

        RateLimiter::clear($key);

        // PB-03: akun nonaktif tidak bisa login.
        if (! $request->user()->is_active) {
            $this->logout($request);

            throw ValidationException::withMessages([
                'username' => 'Akun ini sudah dinonaktifkan. Hubungi operator sekolah.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route($request->user()->homeRoute()));
    }

    /** FR-02: keluar dari akun. */
    public function destroy(Request $request): RedirectResponse
    {
        $this->logout($request);

        return redirect()->route('login');
    }

    private function logout(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
