<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\RegistrationSetting;
use App\Models\Rombel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * M01d, M01e: pendaftaran akun mandiri (FR-29, PB-30).
 * Hanya bisa dipakai saat admin membuka pendaftaran (FR-30).
 */
class RegisterController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (! RegistrationSetting::isOpen()) {
            return $this->closed();
        }

        return view('auth.daftar', ['rombels' => Rombel::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (! RegistrationSetting::isOpen()) {
            return $this->closed();
        }

        $isSiswa = $request->input('role') === Role::Siswa->value;

        $data = $request->validate([
            'role' => ['required', Rule::in([Role::Siswa->value, Role::Guru->value])],
            'name' => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'string',
                $isSiswa ? 'regex:/^\d{10}$/' : 'regex:/^[A-Za-z0-9._-]{3,50}$/',
                Rule::unique('users', 'username'),
            ],
            'rombel_id' => [$isSiswa ? 'required' : 'nullable', 'exists:rombels,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'role.required' => 'Pilih daftar sebagai siswa atau guru.',
            'name.required' => 'Nama wajib diisi.',
            'username.required' => $isSiswa ? 'NISN wajib diisi.' : 'NIP atau username wajib diisi.',
            'username.regex' => $isSiswa
                ? 'NISN harus 10 digit angka.'
                : 'Username hanya boleh huruf, angka, titik, strip, atau garis bawah (3–50 karakter).',
            'username.unique' => $isSiswa
                ? 'NISN ini sudah terdaftar. Silakan masuk, atau minta bantuan operator sekolah.'
                : 'NIP/username ini sudah terdaftar.',
            'rombel_id.required' => 'Pilih rombel.',
            'rombel_id.exists' => 'Rombel tidak ditemukan.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Ulangi kata sandi tidak sama.',
        ]);

        // Siswa langsung aktif (PB-30). Guru menunggu diaktifkan admin di Kelola akun,
        // supaya siswa tidak bisa iseng mendaftar sebagai guru.
        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'role' => $data['role'],
            'rombel_id' => $isSiswa ? $data['rombel_id'] : null,
            'is_active' => $isSiswa,
            'must_change_password' => false,
            'password' => $data['password'],
        ]);

        return redirect()->route('register.success')->with('registered', [
            'name' => $user->name,
            'username' => $user->username,
            'pending' => ! $user->is_active,
        ]);
    }

    /** M01e: daftar berhasil. */
    public function success(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('registered')) {
            return redirect()->route('login');
        }

        return view('auth.daftar-berhasil', ['account' => $request->session()->get('registered')]);
    }

    private function closed(): RedirectResponse
    {
        return redirect()->route('login')->with('status', 'Pendaftaran akun sedang ditutup. Hubungi operator sekolah.');
    }
}
