
@props(['active' => 'akun'])

@php
    $user = auth()->user();
    $isSiswa = $user->isSiswa();
@endphp

<nav
    aria-label="Navigasi utama"
    class="fixed inset-x-0 bottom-0 z-50 border-t border-slate-200 bg-white">

    <div class="mx-auto grid h-[69px] w-full max-w-[390px] grid-cols-5 px-1">

        {{-- BERANDA --}}
        <a
            href="{{ $isSiswa ? route('siswa.beranda') : route('guru.beranda') }}"
            @if ($active === 'beranda') aria-current="page" @endif
            class="flex flex-col items-center justify-center gap-0.5 {{ $active === 'beranda' ? 'text-blue-600' : 'text-slate-600' }}">

            @if ($active === 'beranda')
                <span class="flex h-8 w-14 items-center justify-center rounded-full bg-blue-100">
            @endif

            <svg viewBox="0 0 24 24" class="size-6"
                fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round">

                <path d="m3 10 9-7 9 7"/>
                <path d="M5 9v11h14V9"/>
                <path d="M9 20v-7h6v7"/>

            </svg>

            @if ($active === 'beranda')
                </span>
            @endif

            <span class="text-sm {{ $active === 'beranda' ? 'font-semibold' : 'font-medium' }}">
                Beranda
            </span>
        </a>


        {{-- MENU KEDUA --}}
        <a
            href="{{ $isSiswa ? route('siswa.tugas') : '#' }}"
            @if ($active === ($isSiswa ? 'tugas' : 'penilaian')) aria-current="page" @endif
            class="flex flex-col items-center justify-center gap-0.5 {{ $active === ($isSiswa ? 'tugas' : 'penilaian') ? 'text-blue-600' : 'text-slate-600' }}">

            @if ($active === ($isSiswa ? 'tugas' : 'penilaian'))
                <span class="flex h-8 w-14 items-center justify-center rounded-full bg-blue-100">
            @endif

            <svg viewBox="0 0 24 24" class="size-6"
                fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round">

                <rect x="5" y="4" width="14" height="17" rx="2"/>
                <path d="M9 4.5V3h6v1.5"/>
                <path d="m9 12 2 2 4-4"/>

            </svg>

            @if ($active === ($isSiswa ? 'tugas' : 'penilaian'))
                </span>
            @endif

            <span class="text-sm {{ $active === ($isSiswa ? 'tugas' : 'penilaian') ? 'font-semibold' : 'font-medium' }}">
                {{ $isSiswa ? 'Tugas' : 'Penilaian' }}
            </span>
        </a>


        {{-- MENU KETIGA --}}
        <a
            href="{{ $isSiswa ? route('siswa.nilai') : '#' }}"
            @if ($active === ($isSiswa ? 'nilai' : 'rekap')) aria-current="page" @endif
            class="flex flex-col items-center justify-center gap-0.5 {{ $active === ($isSiswa ? 'nilai' : 'rekap') ? 'text-blue-600' : 'text-slate-600' }}">

            @if ($active === ($isSiswa ? 'nilai' : 'rekap'))
                <span class="flex h-8 w-14 items-center justify-center rounded-full bg-blue-100">
            @endif

            @if ($isSiswa)
                {{-- ICON NILAI SISWA --}}
                <svg viewBox="0 0 24 24" class="size-6"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">

                    <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/>

                </svg>
            @else
                {{-- ICON REKAP GURU --}}
                <svg viewBox="0 0 24 24" class="size-6"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">

                    <path d="M4 20V10M10 20V4M16 20v-7M22 20V8"/>

                </svg>
            @endif

            @if ($active === ($isSiswa ? 'nilai' : 'rekap'))
                </span>
            @endif

            <span class="text-sm {{ $active === ($isSiswa ? 'nilai' : 'rekap') ? 'font-semibold' : 'font-medium' }}">
                {{ $isSiswa ? 'Nilai' : 'Rekap' }}
            </span>
        </a>


        {{-- NOTIFIKASI --}}
        <a
            href="#"
            @if ($active === 'notifikasi') aria-current="page" @endif
            class="flex flex-col items-center justify-center gap-0.5 {{ $active === 'notifikasi' ? 'text-blue-600' : 'text-slate-600' }}">

            @if ($active === 'notifikasi')
                <span class="flex h-8 w-14 items-center justify-center rounded-full bg-blue-100">
            @endif

            <svg viewBox="0 0 24 24" class="size-6"
                fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round">

                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                <path d="M10 21h4"/>

            </svg>

            @if ($active === 'notifikasi')
                </span>
            @endif

            <span class="text-sm {{ $active === 'notifikasi' ? 'font-semibold' : 'font-medium' }}">
                Notifikasi
            </span>
        </a>


        {{-- AKUN --}}
        <a
            href="{{ route('akun') }}"
            @if ($active === 'akun') aria-current="page" @endif
            class="flex flex-col items-center justify-center gap-0.5 {{ $active === 'akun' ? 'text-blue-600' : 'text-slate-600' }}">

            @if ($active === 'akun')
                <span class="flex h-8 w-14 items-center justify-center rounded-full bg-blue-100">
            @endif

            <svg viewBox="0 0 24 24" class="size-6"
                fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round">

                <circle cx="12" cy="8" r="4"/>
                <path d="M5 21v-2a7 7 0 0 1 14 0v2"/>

            </svg>

            @if ($active === 'akun')
                </span>
            @endif

            <span class="text-sm {{ $active === 'akun' ? 'font-semibold' : 'font-medium' }}">
                Akun
            </span>
        </a>

    </div>
</nav>
