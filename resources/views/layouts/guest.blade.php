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
    <span class="pointer-events-none absolute -top-24 -left-24 size-72 rounded-full bg-brand-100" aria-hidden="true"></span>
    <span class="pointer-events-none absolute top-40 -right-16 size-40 rounded-full bg-sunny-50" aria-hidden="true"></span>

    <main class="relative mx-auto flex min-h-dvh w-full max-w-md flex-col justify-center px-safe pt-[max(32px,env(safe-area-inset-top))] pb-[max(32px,env(safe-area-inset-bottom))]">

        <header class="mb-6 text-center">
            <span class="mx-auto flex size-24 items-center justify-center rounded-[30px] bg-surface shadow-card motion-safe:animate-pop">
                <img src="{{ asset('icons/logo-sdn.png') }}" alt="Logo SDN 002 Karangmulya" width="80" height="80" class="size-20 object-contain">
            </span>
            <p class="mt-3 text-caption font-extrabold tracking-wide text-brand uppercase">SDN 002 Karangmulya</p>
            <h1 class="mt-1 text-h1 font-extrabold">@yield('heading', 'Belajar online untuk siswa dan guru')</h1>
            @hasSection('subheading')
                <p class="mt-1 text-text-muted">@yield('subheading')</p>
            @endif
        </header>

        @yield('content')
    </main>

    <x-feedback :status-toast="! View::hasSection('inline-status')" />

</body>
</html>
