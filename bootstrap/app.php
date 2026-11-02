<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Middleware\EnsurePasswordChanged;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Yang sudah login lalu buka /login diarahkan ke berandanya
        $middleware->redirectUsersTo(fn (Request $request) => route($request->user()->homeRoute()));

        // NFR-09: sesi habis setelah 2 jam tidak aktif. Kalau browser ini pernah masuk
        // (dan belum menekan Keluar), beri tahu kenapa tiba-tiba kembali ke Login.
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->cookie(LoginController::SESSION_COOKIE)) {
                $request->session()->flash('status', LoginController::SESSION_EXPIRED);
                Cookie::queue(Cookie::forget(LoginController::SESSION_COOKIE));
            }

            return route('login');
        });

        $middleware->alias([
            // Cek peran: ->middleware('role:guru')
            'role' => EnsureUserHasRole::class,
            // Wajib ganti kata sandi awal dulu
            'password.changed' => EnsurePasswordChanged::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Form dikirim setelah sesi habis (token CSRF sudah tidak berlaku)
        // (Laravel mengubahnya jadi HttpException 419 sebelum sampai ke sini)
        $exceptions->render(function (HttpException $e, Request $request) {
            if (! $e->getPrevious() instanceof TokenMismatchException) {
                return null;
            }

            if ($request->user() === null) {
                Cookie::queue(Cookie::forget(LoginController::SESSION_COOKIE));

                return redirect()->route('login')->with('status', LoginController::SESSION_EXPIRED);
            }

            return back()->withInput($request->except('password', 'password_confirmation', 'current_password'))
                ->withErrors(['session' => 'Halaman sudah terlalu lama dibuka. Coba kirim lagi.']);
        });

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();