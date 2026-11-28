{{--
    Navigasi bawah per peran. Menu yang sedang dibuka disorot (ikon penuh + latar biru muda).
    Ada ruang untuk garis gestur / home indicator HP (pb-safe).
--}}
@props(['unread' => 0])

@php
    $user = auth()->user();
    $notifikasi = ['label' => 'Notifikasi', 'icon' => 'bell', 'route' => 'notifikasi.index', 'active' => ['notifikasi.*'], 'badge' => $unread];
    $akun = ['label' => 'Akun', 'icon' => 'user-circle', 'route' => 'akun', 'active' => ['akun', 'akun.*']];

    $items = match (true) {
        $user->isSiswa() => [
            ['label' => 'Beranda', 'icon' => 'house', 'route' => 'siswa.beranda', 'active' => ['siswa.beranda', 'kelas.*']],
            ['label' => 'Tugas', 'icon' => 'clipboard-text', 'route' => 'siswa.tugas', 'active' => ['siswa.tugas']],
            ['label' => 'Nilai', 'icon' => 'trophy', 'route' => 'siswa.nilai', 'active' => ['siswa.nilai']],
            $notifikasi,
            $akun,
        ],
        $user->isGuru() => [
            ['label' => 'Beranda', 'icon' => 'house', 'route' => 'guru.beranda', 'active' => ['guru.beranda', 'guru.buat-kelas', 'kelas.*']],
            ['label' => 'Penilaian', 'icon' => 'list-checks', 'route' => 'guru.penilaian', 'active' => ['guru.penilaian']],
            ['label' => 'Rekap', 'icon' => 'chart-bar', 'route' => 'guru.rekap', 'active' => ['guru.rekap', 'kelas.rekap', 'kelas.rekap.*']],
            $notifikasi,
            $akun,
        ],
        default => [
            ['label' => 'Beranda', 'icon' => 'house', 'route' => 'admin.beranda', 'active' => ['admin.beranda', 'admin.mapel.*', 'admin.rombel.*']],
            ['label' => 'Pengguna', 'icon' => 'users-three', 'route' => 'admin.akun.index', 'active' => ['admin.akun.*', 'admin.impor.*']],
            ['label' => 'Kelas', 'icon' => 'chalkboard-teacher', 'route' => 'admin.kelas.index', 'active' => ['admin.kelas.*']],
            $notifikasi,
            $akun,
        ],
    };
@endphp

{{-- Pil biru yang melayang di atas konten; menu aktif jadi kapsul putih berisi ikon + label --}}
<nav aria-label="Navigasi utama" class="pointer-events-none fixed inset-x-0 bottom-nav-offset z-40 px-safe">
    <ul class="pointer-events-auto mx-auto flex h-[68px] max-w-md items-center justify-between rounded-full bg-brand px-2 shadow-[0_10px_30px_rgb(37_99_235/0.35)]">
        @foreach ($items as $item)
            @php($active = request()->routeIs(...$item['active']))
            <li @class(['flex', 'flex-1' => ! $active])>
                <a href="{{ route($item['route']) }}" @if ($active) aria-current="page" @endif
                    @class([
                        'relative flex h-12 items-center justify-center gap-1.5 rounded-full transition',
                        'bg-white px-4 text-brand' => $active,
                        'w-full text-white/75 hover:text-white active:bg-white/15' => ! $active,
                    ])>
                    <x-icon :name="$item['icon']" :variant="$active ? 'fill' : 'regular'" class="size-6" />
                    {{-- Label selalu ada untuk pembaca layar; tampil hanya di menu aktif supaya tidak sesak --}}
                    <span @class(['text-caption font-extrabold', 'sr-only' => ! $active])>{{ $item['label'] }}</span>
                    @if (($item['badge'] ?? 0) > 0)
                        <span class="absolute top-1 right-[calc(50%-20px)] flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-danger px-1 text-[11px] leading-none font-extrabold text-white ring-2 ring-brand">
                            {{ $item['badge'] > 9 ? '9+' : $item['badge'] }}<span class="sr-only"> belum dibaca</span>
                        </span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</nav>
