<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * Menu utama per peran, dipakai navigasi bawah (HP) dan sidebar (desktop).
 * Satu menu yang aktif: yang terakhir cocok dengan route sekarang, supaya menu yang lebih
 * spesifik menang (mis. kelas.rekap ikut pola kelas.* milik Beranda, tapi yang aktif Rekap).
 */
class Navigation
{
    /**
     * @return list<array{label: string, icon: string, route: string, badge: int, active: bool}>
     */
    public static function for(User $user, Request $request, int $unread = 0): array
    {
        $items = match (true) {
            $user->isSiswa() => [
                ['Beranda', 'house', 'siswa.beranda', ['siswa.beranda', 'kelas.*']],
                ['Tugas', 'clipboard-text', 'siswa.tugas', ['siswa.tugas']],
                ['Nilai', 'trophy', 'siswa.nilai', ['siswa.nilai']],
            ],
            $user->isGuru() => [
                ['Beranda', 'house', 'guru.beranda', ['guru.beranda', 'guru.buat-kelas', 'kelas.*']],
                ['Penilaian', 'list-checks', 'guru.penilaian', ['guru.penilaian']],
                ['Rekap', 'chart-bar', 'guru.rekap', ['guru.rekap', 'kelas.rekap', 'kelas.rekap.*']],
            ],
            default => [
                ['Beranda', 'house', 'admin.beranda', ['admin.beranda', 'admin.mapel.*', 'admin.rombel.*']],
                ['Pengguna', 'users-three', 'admin.akun.index', ['admin.akun.*', 'admin.impor.*']],
                ['Kelas', 'chalkboard-teacher', 'admin.kelas.index', ['admin.kelas.*']],
            ],
        };
        $items[] = ['Notifikasi', 'bell', 'notifikasi.index', ['notifikasi.*']];
        $items[] = ['Akun', 'user-circle', 'akun', ['akun', 'akun.*']];

        $activeIndex = null;
        foreach ($items as $index => [, , , $patterns]) {
            if ($request->routeIs(...$patterns)) {
                $activeIndex = $index;
            }
        }

        return array_map(fn (array $item, int $index) => [
            'label' => $item[0],
            'icon' => $item[1],
            'route' => $item[2],
            'badge' => $item[2] === 'notifikasi.index' ? $unread : 0,
            'active' => $index === $activeIndex,
        ], $items, array_keys($items));
    }
}
