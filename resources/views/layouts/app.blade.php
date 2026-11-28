{{--
    Kerangka aplikasi (app shell) untuk semua halaman setelah login.
    - viewport-fit=cover + pt-safe/pb-safe: aman untuk HP ber-notch & garis gestur.
    - Top bar hanya tombol kembali + judul (+ aksi opsional). Akun & Keluar ada di tab Akun.
    - Navigasi bawah melayang per peran, pesan sukses jadi toast, error & konfirmasi jadi bottom sheet.

    Section yang bisa diisi halaman:
      title         judul di top bar & tab browser (wajib)
      back          URL tombol kembali (opsional; tanpa ini tidak ada tombol kembali)
      back-label    teks untuk pembaca layar, mis. "Kembali ke Matematika 4A"
      header        isi top bar pengganti judul, mis. logo sekolah (opsional)
      header-action tombol di kanan top bar (opsional)
      topbar        "none": tanpa top bar (halaman punya sapaan sendiri, mis. beranda siswa)
                    "overlay": top bar transparan di atas hero berwarna
      hero          blok berwarna selebar layar di atas isi (dipakai bersama topbar "overlay")
      width         "narrow": isi dibatasi lebar baca nyaman di desktop (halaman form)
      content       isi halaman
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    @include('partials.head')

    <title>@yield('title') | SDN 002 Karangmulya</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
    $withNav = auth()->check() && ! auth()->user()->must_change_password;
    $topbar = trim($__env->yieldContent('topbar')) ?: 'default';
    $desktopWidth = trim($__env->yieldContent('width')) === 'narrow' ? 'lg:max-w-3xl' : 'lg:max-w-6xl';
@endphp

<body class="min-h-dvh bg-background font-sans text-body text-text antialiased">

    <a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:rounded-control focus:bg-surface focus:px-4 focus:py-3 focus:shadow-raised">
        Lompat ke isi halaman
    </a>

    @if ($withNav)
        <x-sidebar :unread="$unreadNotifications" />
    @endif

    {{-- Kolom konten; di layar lebar bergeser ke kanan sidebar --}}
    <div @class(['relative', 'lg:pl-72' => $withNav])>

    @if ($topbar !== 'none')
        <header @class([
            'top-0 z-30 pt-safe',
            'sticky bg-background/90 backdrop-blur' => $topbar === 'default',
            'absolute inset-x-0 text-white' => $topbar === 'overlay',
            'lg:left-72' => $topbar === 'overlay' && $withNav,
        ])>
            <div class="mx-auto flex h-16 max-w-3xl items-center gap-3 px-safe lg:h-20 lg:px-10 {{ $desktopWidth }}">
                @hasSection('back')
                    <a href="@yield('back')" aria-label="@yield('back-label', 'Kembali')"
                        @class([
                            'flex size-11 shrink-0 items-center justify-center rounded-full transition',
                            'bg-surface text-text shadow-card active:scale-95' => $topbar === 'default',
                            'bg-white/20 text-white backdrop-blur active:bg-white/30' => $topbar === 'overlay',
                        ])>
                        <x-icon name="arrow-left" variant="bold" class="size-5" />
                    </a>
                @endif

                @hasSection('header')
                    {{-- Logo di top bar hanya di HP; di desktop logo sudah ada di sidebar --}}
                    <div class="min-w-0 flex-1 lg:hidden">@yield('header')</div>
                    <p class="hidden min-w-0 flex-1 truncate text-h1 font-extrabold lg:block">@yield('title')</p>
                @else
                    <p @class(['min-w-0 flex-1 truncate text-h2 font-extrabold lg:text-h1', 'sr-only' => $topbar === 'overlay'])>@yield('title')</p>
                @endif

                @yield('header-action')
            </div>
        </header>
    @endif

    @yield('hero')

    <main id="konten" @class([
        'relative mx-auto max-w-3xl space-y-4 px-safe lg:space-y-6 lg:px-10',
        $desktopWidth,
        'pt-2' => $topbar === 'default',
        'pt-[max(20px,env(safe-area-inset-top))] lg:pt-10' => $topbar === 'none',
        '-mt-8' => $topbar === 'overlay',
        'pb-nav lg:pb-12' => $withNav,
        'pb-10' => ! $withNav,
    ])>
        @yield('content')
    </main>

    </div>

    @if ($withNav)
        <x-bottom-nav :unread="$unreadNotifications" />
    @endif

    <x-feedback :above-nav="$withNav" />

</body>
</html>
