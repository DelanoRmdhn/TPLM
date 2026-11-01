<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Hanya untuk yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

// Harus login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Siswa
    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::view('/beranda', 'siswa.beranda')->name('beranda');
        Route::view('/tugas', 'siswa.tugas')->name('tugas');
    });

    // Guru
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(function () {
        Route::view('/beranda', 'guru.beranda')->name('beranda');
        Route::view('/buat-kelas', 'guru.buat-kelas')->name('buat-kelas');
    });

    // Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Sementara, sampai halaman admin (Hal 2) di-slicing
        Route::get('/beranda', fn () => 'Halaman admin belum dibuat.')->name('beranda');
    });
});