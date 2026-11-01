<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * M01c: buat kata sandi baru saat login pertama / setelah direset admin.
 */
class NewPasswordController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        // Yang tidak wajib ganti tidak perlu ke halaman ini
        if (! $request->user()->must_change_password) {
            return redirect()->route($request->user()->homeRoute());
        }

        return view('auth.buat-kata-sandi');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Ulangi kata sandi tidak sama.',
        ]);

        if (Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Kata sandi baru tidak boleh sama dengan kata sandi lama.',
            ]);
        }

        $user->update([
            'password' => $request->password,
            'must_change_password' => false,
        ]);

        return redirect()->route($user->homeRoute());
    }
}