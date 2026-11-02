
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Siswa</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">

    {{-- Mobile-first app container --}}
    <div class="mx-auto min-h-screen w-full max-w-[390px] bg-slate-50 pb-20">

        {{-- HEADER --}}
        <header class="flex items-center gap-3 border-b border-slate-200 bg-white px-[18px] py-3.5">
            <div class="flex size-[46px] shrink-0 items-center justify-center rounded-2xl bg-brand text-white">
                <svg viewBox="0 0 24 24" class="size-7" fill="none"
                    stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m2 9 10-5 10 5-10 5L2 9Z"/>
                    <path d="M6 11v5c3.5 3 8.5 3 12 0v-5"/>
                    <path d="M22 9v6"/>
                </svg>
            </div>

            <div class="min-w-0">
                <h1 class="text-[18px] leading-tight font-bold">
                    Halo, {{ auth()->user()->name }}!
                </h1>
                <p class="mt-1 text-sm text-slate-600">
                    Siswa{{ auth()->user()->rombel ? ' · Kelas '.auth()->user()->rombel->name : '' }}
                </p>
            </div>
        </header>

        <main class="space-y-3.5 px-[18px] py-5">

            {{-- TENGGAT TERDEKAT --}}
            @if ($nearest)
            <section class="rounded-2xl bg-amber-100 p-4">
                <div class="flex items-center gap-2 text-amber-700">
                    <svg viewBox="0 0 24 24" class="size-5 shrink-0"
                        fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 7v5l3 2"/>
                    </svg>
                    <span class="text-sm font-medium">Tenggat terdekat</span>
                </div>

                <h2 class="mt-2 text-[17px] leading-snug font-bold">
                    {{ $nearest->assignment->title }}
                </h2>

                <p class="mt-2 text-sm leading-5 text-slate-700">
                    {{ $nearest->assignment->classroom->subject }} · {{ $nearest->dueLabel }}
                </p>

                <a href="{{ route('kelas.tugas.show', [$nearest->assignment->classroom_id, $nearest->assignment]) }}"
                    class="mt-2.5 flex min-h-[50px] items-center justify-center gap-2 rounded-xl bg-brand px-4 text-sm font-semibold text-white transition hover:bg-blue-700">
                    <svg viewBox="0 0 24 24" class="size-5"
                        fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                    Kerjakan Sekarang
                </a>
            </section>
            @endif

            {{-- BELUM DIKERJAKAN (maks 3, urut tenggat terdekat) --}}
            <section>
                <div class="flex items-center justify-between gap-2 py-1">
                    <h2 class="text-[17px] font-bold">
                        Belum dikerjakan
                    </h2>

                    <a href="{{ route('siswa.tugas') }}"
                        class="flex min-h-[46px] shrink-0 items-center rounded-xl bg-slate-100 px-4 text-sm font-semibold text-slate-800 hover:bg-slate-200">
                        Lihat Semua
                    </a>
                </div>

                <div class="mt-3 space-y-3">
                    @forelse ($pending as $row)
                        @include('siswa._kartu-tugas', ['row' => $row])
                    @empty
                        <p class="rounded-2xl border border-slate-200 bg-white p-4 text-sm text-slate-600">
                            Tidak ada tugas yang belum dikerjakan. 🎉
                        </p>
                    @endforelse
                </div>
            </section>

            {{-- KELAS SAYA --}}
            <section>
                <h2 class="py-1 text-[17px] font-bold">
                    Kelas Saya
                </h2>

                <div class="mt-3 space-y-3">

                    @php($warna = ['bg-blue-100 text-blue-700', 'bg-green-100 text-green-700', 'bg-amber-100 text-amber-700'])

                    @forelse ($classrooms as $kelas)
                        <a href="{{ route('kelas.show', $kelas) }}"
                            class="flex items-center gap-3.5 rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-blue-300">
                            <div class="flex size-[50px] shrink-0 items-center justify-center rounded-xl {{ $warna[$loop->index % 3] }}">
                                <svg viewBox="0 0 24 24" class="size-6"
                                    fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 7v14"/>
                                    <path d="M12 7C9 4 5 4 3 5v14c3-1 6-1 9 2"/>
                                    <path d="M12 7c3-3 7-3 9-2v14c-3-1-6-1-9 2"/>
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold">{{ $kelas->name }}</h3>
                                <p class="mt-1 text-sm text-slate-600">
                                    {{ $kelas->subject }} · {{ $kelas->teacher->name }}
                                </p>
                                @if ($belum = $pendingPerClass->get($kelas->id))
                                    <p class="mt-1 text-xs font-semibold text-orange-700">
                                        {{ $belum }} tugas belum dikerjakan
                                    </p>
                                @endif
                            </div>

                            <svg viewBox="0 0 24 24" class="size-5 shrink-0 text-slate-600"
                                fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </a>
                    @empty
                        <p class="rounded-2xl border border-slate-200 bg-white p-4 text-sm text-slate-600">
                            Kamu belum masuk kelas apa pun. Tunggu guru memasukkan rombelmu.
                        </p>
                    @endforelse

                </div>
            </section>

        </main>
    </div>

    {{-- BOTTOM NAVIGATION --}}
<{{-- BOTTOM NAVIGATION --}}
<nav
    aria-label="Navigasi utama"
    class="fixed inset-x-0 bottom-0 z-50 border-t border-slate-200 bg-white">

    <div class="mx-auto grid h-[69px] w-full max-w-[390px] grid-cols-5 px-1">

        {{-- BERANDA AKTIF --}}
        <a
            href="{{ route('siswa.beranda') }}"
            aria-current="page"
            class="flex flex-col items-center justify-center gap-0.5 text-blue-600">

            <span class="flex h-8 w-14 items-center justify-center rounded-full bg-blue-100">
                <svg
                    viewBox="0 0 24 24"
                    class="size-6"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <path d="m3 10 9-7 9 7"/>
                    <path d="M5 9v11h14V9"/>
                    <path d="M9 20v-7h6v7"/>

                </svg>
            </span>

            <span class="text-sm font-semibold">Beranda</span>
        </a>

        {{-- TUGAS --}}
        <a
            href="{{ route('siswa.tugas') }}"
            class="flex flex-col items-center justify-center gap-0.5 text-slate-600">

            <svg
                viewBox="0 0 24 24"
                class="size-6"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round">

                <rect x="5" y="4" width="14" height="17" rx="2"/>
                <path d="M9 4.5V3h6v1.5"/>
                <path d="m9 12 2 2 4-4"/>

            </svg>

            <span class="text-sm font-medium">Tugas</span>
        </a>

        {{-- NILAI --}}
        <a
            href="{{ route('siswa.nilai') }}"
            class="flex flex-col items-center justify-center gap-0.5 text-slate-600">

            <svg
                viewBox="0 0 24 24"
                class="size-6"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round">

                <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/>

            </svg>

            <span class="text-sm font-medium">Nilai</span>
        </a>

        {{-- NOTIFIKASI --}}
        <a
            href="#"
            class="flex flex-col items-center justify-center gap-0.5 text-slate-600">

            <svg
                viewBox="0 0 24 24"
                class="size-6"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round">

                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                <path d="M10 21h4"/>

            </svg>

            <span class="text-sm font-medium">Notifikasi</span>
        </a>

        {{-- AKUN --}}
        <a
            href="{{ route('akun') }}"
            class="flex flex-col items-center justify-center gap-0.5 text-slate-600">

            <svg
                viewBox="0 0 24 24"
                class="size-6"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round">

                <circle cx="12" cy="8" r="4"/>
                <path d="M5 21v-2a7 7 0 0 1 14 0v2"/>

            </svg>

            <span class="text-sm font-medium">Akun</span>
        </a>

    </div>
</nav>
</body>
</html>
