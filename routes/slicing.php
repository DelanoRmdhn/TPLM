<?php

use Illuminate\Support\Facades\Route;

/*
 | Pratinjau hasil slicing Figma (frontend saja, data contoh).
 | Hanya aktif di lingkungan local: /slicing
 */
$pages = [
    'MUST · wajib saat kunjungan sekolah' => [
        'm01-login' => 'M01 · Masuk',
        'm01b-login-salah' => 'M01b · Masuk, data salah',
        'm01c-buat-kata-sandi' => 'M01c · Buat kata sandi baru',
        'm01d-daftar' => 'M01d · Daftar akun (siswa)',
        'm01d2-daftar-guru' => 'M01d · Daftar akun (guru)',
        'm01e-daftar-berhasil' => 'M01e · Pendaftaran berhasil',
        'm01f-login-ditutup' => 'M01f · Masuk, pendaftaran ditutup',
        'm02a-beranda-siswa' => 'M02a · Beranda siswa',
        'm02b-beranda-guru' => 'M02b · Beranda guru',
        'm03-buat-kelas' => 'M03 · Buat kelas',
        'm03a-kelas-siswa' => 'M03a · Kelas (siswa)',
        'm03b-kelas-guru' => 'M03b · Kelas (guru)',
        'm03c-materi-1' => 'M03c · Buka materi, halaman 1',
        'm03c2-materi-2' => 'M03c2 · Buka materi, halaman 2',
        'm04-buat-tugas' => 'M04 · Buat tugas',
        'm04b-tambah-materi' => 'M04b · Tambah materi',
        'm05-detail-tugas' => 'M05 · Detail tugas (siswa)',
        'm05a-tugas-saya' => 'M05a · Tugas saya',
        'm06-pengumpulan-guru' => 'M06 · Pengumpulan dan penilaian',
        'm07-nilai-saya' => 'M07 · Nilai saya',
        'm08-admin-kelola-akun' => 'M08 · Kelola akun',
        'm08b-hasil-impor' => 'M08b · Hasil impor Excel',
        'm08c-kata-sandi-sementara' => 'M08c · Kata sandi sementara',
        'm08d-konfirmasi-nonaktif' => 'M08d · Konfirmasi nonaktifkan',
        'm08e-admin-ditutup' => 'M08e · Kelola akun, pendaftaran ditutup',
    ],
    'SHOULD · diusahakan selesai sebelum kunjungan' => [
        's01-forum-siswa' => 'S01 · Forum kelas (siswa)',
        's02-forum-guru' => 'S02 · Forum kelas (guru)',
        's03-rekap-nilai' => 'S03 · Rekap nilai',
        's04-notifikasi-siswa' => 'S04 · Notifikasi (siswa)',
        's05-notifikasi-guru' => 'S05 · Notifikasi (guru)',
        's06-tugas-diserahkan' => 'S06 · Tugas diserahkan',
        's07-buat-tugas-jadwal' => 'S07 · Jadwalkan tugas',
        's08-pengaturan-kelas' => 'S08 · Pengaturan kelas',
        's09-anggota-kelas' => 'S09 · Anggota kelas',
        's10-akun-siswa' => 'S10 · Akun (siswa)',
        's11-akun-guru' => 'S11 · Akun (guru)',
        's12-akun-admin' => 'S12 · Akun (admin)',
    ],
    'COULD · dikerjakan kalau waktu cukup' => [
        'c01-ringkasan-admin' => 'C01 · Ringkasan aktivitas',
        'c02-gabung-kelas' => 'C02 · Gabung kelas dengan kode',
        'c03-mode-offline' => 'C03 · Mode offline',
    ],
    'OVERLAY · komponen' => [
        'overlay-popup' => 'Pop-up umpan balik tombol',
        'komponen' => 'Pustaka komponen',
    ],
];

if (app()->environment('local')) {
    Route::prefix('slicing')->name('slicing.')->group(function () use ($pages) {
        Route::get('/', fn () => view('slicing.index', ['groups' => $pages]))->name('index');

        Route::get('/{page}', function (string $page) use ($pages) {
            abort_unless(collect($pages)->contains(fn ($group) => isset($group[$page])), 404);

            return view("slicing.$page");
        })->where('page', '[a-z0-9-]+')->name('show');
    });
}
