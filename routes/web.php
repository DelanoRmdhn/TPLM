<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\RegistrationSettingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Hanya untuk yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    // M01d, M01e: pendaftaran mandiri (hanya saat dibuka admin)
    Route::get('/daftar', [RegisterController::class, 'show'])->name('register');
    Route::post('/daftar', [RegisterController::class, 'store'])->middleware('throttle:20,1')->name('register.store');
    Route::get('/daftar/berhasil', [RegisterController::class, 'success'])->name('register.success');
});

// Harus login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // M01c: buat kata sandi baru (login pertama / setelah direset)
    Route::get('/kata-sandi/baru', [NewPasswordController::class, 'create'])->name('password.create');
    Route::post('/kata-sandi/baru', [NewPasswordController::class, 'store'])->name('password.store');

    // Semua halaman di bawah ini: kata sandi awal harus sudah diganti
    Route::middleware('password.changed')->group(function () {

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
            Route::redirect('/beranda', '/admin/akun')->name('beranda');

            // M08, M08c, M08d: kelola akun
            Route::get('/akun', [AccountController::class, 'index'])->name('akun.index');
            Route::get('/akun/tambah', [AccountController::class, 'create'])->name('akun.create');
            Route::post('/akun', [AccountController::class, 'store'])->name('akun.store');
            Route::get('/akun/{user}/ubah', [AccountController::class, 'edit'])->name('akun.edit');
            Route::put('/akun/{user}', [AccountController::class, 'update'])->name('akun.update');
            Route::post('/akun/{user}/nonaktifkan', [AccountController::class, 'deactivate'])->name('akun.deactivate');
            Route::post('/akun/{user}/aktifkan', [AccountController::class, 'activate'])->name('akun.activate');
            Route::post('/akun/{user}/reset-kata-sandi', [AccountController::class, 'resetPassword'])->name('akun.reset-password');

            // M08e: buka/tutup pendaftaran
            Route::put('/pendaftaran', [RegistrationSettingController::class, 'update'])->name('pendaftaran.update');

            // M08, M08b: impor Excel
            Route::get('/impor', [ImportController::class, 'index'])->name('impor.index');
            Route::get('/impor/template/{type}', [ImportController::class, 'template'])->name('impor.template');
            Route::post('/impor', [ImportController::class, 'store'])->name('impor.store');
            Route::get('/impor/hasil', [ImportController::class, 'result'])->name('impor.hasil');
            Route::get('/impor/unduh', [ImportController::class, 'download'])->name('impor.unduh');
        });
    });
});
