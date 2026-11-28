{{--
    Kerangka halaman tanpa navigasi: login, daftar, buat kata sandi awal.
    Logo sekolah di atas, isi di tengah, aman untuk HP ber-notch (safe area).

    Section: title (wajib), heading (judul besar di bawah logo), subheading, content.
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

<body class="relative min-h-dvh overflow-x-hidden bg-background font-sans text-body text-text antialiased">

    {{-- Hiasan latar --}}
    <span class="pointer-events-none absolute -top-24 -left-24 size-72 rounded-full bg-brand-100 lg:hidden" aria-hidden="true"></span>
    <span class="pointer-events-none absolute top-40 -right-16 size-40 rounded-full bg-sunny-50" aria-hidden="true"></span>

    <div class="lg:grid lg:min-h-dvh lg:grid-cols-2">

        {{-- Panel sekolah: hanya di layar lebar --}}
        <aside class="relative hidden overflow-hidden bg-brand p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <span class="pointer-events-none absolute -top-24 -right-24 size-96 rounded-full bg-white/10" aria-hidden="true"></span>
            <span class="pointer-events-none absolute -bottom-32 -left-20 size-96 rounded-full bg-white/10" aria-hidden="true"></span>
            <span class="pointer-events-none absolute right-24 bottom-40 size-24 rounded-full bg-sunny/40" aria-hidden="true"></span>

            <div class="relative flex items-center gap-4">
                <span class="flex size-20 items-center justify-center rounded-[26px] bg-white shadow-raised">
                    <img src="{{ asset('icons/logo-sdn.png') }}" alt="" width="68" height="68" class="size-[68px] object-contain">
                </span>
                <span>
                    <span class="block text-h1 font-extrabold">SDN 002 Karangmulya</span>
                    <span class="block font-semibold text-white/80">Kota Bandung</span>
                </span>
            </div>

            <div class="relative max-w-md">
                <p class="text-[40px] leading-tight font-extrabold">Belajar jadi seru, di mana saja.</p>
                <ul class="mt-8 space-y-4 text-[17px] font-semibold">
                    @foreach ([
                        ['clipboard-text', 'Lihat tugas dan tenggatnya dengan jelas'],
                        ['camera', 'Kumpulkan tugas cukup dengan foto dari HP'],
                        ['trophy', 'Nilai dan pesan dari guru langsung terlihat'],
                    ] as [$icon, $text])
                        <li class="flex items-center gap-3">
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-[14px] bg-white/15">
                                <x-icon :name="$icon" variant="fill" class="size-6 text-sunny" />
                            </span>
                            {{ $text }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <p class="relative text-caption text-white/70">Masuk memakai NISN, tanpa email.</p>
        </aside>

        <main class="relative mx-auto flex min-h-dvh w-full max-w-md flex-col justify-center px-safe pt-[max(32px,env(safe-area-inset-top))] pb-[max(32px,env(safe-area-inset-bottom))] lg:py-12">

            <header class="mb-6 text-center lg:text-left">
                <span class="mx-auto flex size-24 items-center justify-center rounded-[30px] bg-surface shadow-card motion-safe:animate-pop lg:hidden">
                    <img src="{{ asset('icons/logo-sdn.png') }}" alt="Logo SDN 002 Karangmulya" width="80" height="80" class="size-20 object-contain">
                </span>
                <p class="mt-3 text-caption font-extrabold tracking-wide text-brand uppercase lg:hidden">SDN 002 Karangmulya</p>
                <h1 class="mt-1 text-h1 font-extrabold lg:text-display">@yield('heading', 'Belajar online untuk siswa dan guru')</h1>
                @hasSection('subheading')
                    <p class="mt-1 text-text-muted">@yield('subheading')</p>
                @endif
            </header>

            @yield('content')
        </main>
    </div>

    <x-feedback :status-toast="! View::hasSection('inline-status')" />

</body>
</html>
