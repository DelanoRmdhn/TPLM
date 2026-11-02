@props(['role' => 'siswa', 'active' => 'beranda'])

{{-- Organisme: navigasi bawah (Komponen · Navigasi/Siswa, /Guru, /Admin) --}}
@php
    $menus = [
        'siswa' => [
            'beranda' => ['Beranda', 'home'],
            'tugas' => ['Tugas', 'clipboard-check'],
            'nilai' => ['Nilai', 'star'],
            'notifikasi' => ['Notifikasi', 'bell'],
            'akun' => ['Akun', 'user'],
        ],
        'guru' => [
            'beranda' => ['Beranda', 'home'],
            'penilaian' => ['Penilaian', 'clipboard-check'],
            'rekap' => ['Rekap', 'chart'],
            'notifikasi' => ['Notifikasi', 'bell'],
            'akun' => ['Akun', 'user'],
        ],
        'admin' => [
            'pengguna' => ['Pengguna', 'users'],
            'ringkasan' => ['Ringkasan', 'chart'],
            'akun' => ['Akun', 'user'],
        ],
    ];
@endphp

<nav aria-label="Navigasi utama" class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white">
    <div class="mx-auto flex h-[72px] w-full max-w-[360px] items-center px-1">
        @foreach ($menus[$role] as $key => [$label, $icon])
            <x-molecules.nav-item :icon="$icon" :label="$label" :active="$key === $active" />
        @endforeach
    </div>
</nav>
