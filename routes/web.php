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

    Route::view('/siswa/beranda', 'siswa.beranda')->name('siswa.beranda');
    Route::view('/siswa/tugas', 'siswa.tugas')->name('siswa.tugas');

    Route::view('/guru/beranda', 'guru.beranda')->name('guru.beranda');
    Route::view('/guru/buat-kelas', 'guru.buat-kelas')->name('guru.buat-kelas');

    // Sementara, sampai halaman admin (Hal 2) di-slicing
    Route::get('/admin/beranda', fn () => 'Halaman admin belum dibuat.')->name('admin.beranda');
});