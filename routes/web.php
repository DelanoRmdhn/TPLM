<?php
use Illuminate\Support\Facades\Route;

Route::view('/login', 'auth.login')->name('login');

Route::view('/siswa/beranda', 'siswa.beranda')
    ->name('siswa.beranda');

Route::view('/guru/beranda', 'guru.beranda')
    ->name('guru.beranda');

Route::view('/guru/beranda', 'guru.beranda')
    ->name('guru.beranda');

Route::view('/guru/buat-kelas', 'guru.buat-kelas')
    ->name('guru.buat-kelas');

Route::view('/siswa/beranda', 'siswa.beranda')
    ->name('siswa.beranda');

Route::view('/siswa/tugas', 'siswa.tugas')
    ->name('siswa.tugas');