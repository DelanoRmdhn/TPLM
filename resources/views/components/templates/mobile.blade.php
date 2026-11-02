@props(['title' => 'SDN 002 Karangmulya', 'nav' => null, 'active' => null])

{{-- Template: bingkai layar HP 360 px. Slot: header (bilah atas), isi, overlay (pop-up / lembar bawah). --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} — SDN 002 Karangmulya</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-200 font-sans text-base text-slate-900 antialiased">

    <div class="relative mx-auto flex min-h-screen w-full max-w-[360px] flex-col bg-slate-50">
        {{ $header ?? '' }}

        <main class="flex-1 space-y-4 px-4 pt-4 {{ $nav ? 'pb-28' : 'pb-8' }}">
            {{ $slot }}
        </main>

        @if ($nav)
            <x-organisms.bottom-nav :role="$nav" :active="$active" />
        @endif
    </div>

    {{ $overlay ?? '' }}

</body>
</html>
