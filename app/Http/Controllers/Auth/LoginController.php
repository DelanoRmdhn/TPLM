<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RegistrationSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /** Cookie penanda pengguna pernah masuk di browser ini; dihapus saat menekan Keluar. */
    public const SESSION_COOKIE = 'pernah_masuk';

    public const SESSION_EXPIRED = 'Sesi kamu sudah berakhir. Silakan masuk lagi.';

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
                'username' => 'Akun kamu tidak aktif. Hubungi admin sekolah.',
            ]);
        }

        $request->session()->regenerate();

        // Penanda "pernah masuk" untuk pesan "Sesi kamu sudah berakhir" (lihat bootstrap/app.php)
        Cookie::queue(self::SESSION_COOKIE, '1', 60 * 24 * 30);

        return redirect()->intended(route($request->user()->homeRoute()));
    }

    /** FR-02: keluar dari akun. */
    public function destroy(Request $request): RedirectResponse
    {
        $this->logout($request);

        // Keluar sendiri, jadi tidak perlu pesan sesi berakhir
        Cookie::queue(Cookie::forget(self::SESSION_COOKIE));

        return redirect()->route('login');
    }

    private function logout(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
