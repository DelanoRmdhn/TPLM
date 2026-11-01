<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * PB-02, PB-04: akun dengan kata sandi awal/sementara wajib ganti
 * kata sandi dulu sebelum bisa membuka halaman lain.
 */
class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->must_change_password) {
            return redirect()->route('password.create');
        }

        return $next($request);
    }
}