<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * S14, FR-03: akun saya & ganti kata sandi sendiri (semua peran).
 */
class AccountSettingsController extends Controller
{
    public function show(Request $request): View
    {
        return view('akun.show', ['user' => $request->user()->load('rombel')]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi lama wajib diisi.',
            'current_password.current_password' => 'Kata sandi lama salah.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Ulangi kata sandi baru tidak sama.',
        ]);

        $user = $request->user();

        if (Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Kata sandi baru tidak boleh sama dengan kata sandi lama.',
            ]);
        }

        $user->update(['password' => $request->password]);

        // Keluarkan perangkat lain yang masih login dengan kata sandi lama
        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        return back()->with('status', 'Kata sandi berhasil diganti.');
    }
}
