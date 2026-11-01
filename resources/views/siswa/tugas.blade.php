
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas Saya</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">

    <div class="mx-auto min-h-screen w-full max-w-[390px] bg-slate-50 pb-[76px]">

        {{-- HEADER --}}
        <header class="border-b border-slate-200 bg-white px-[18px] py-5">
            <h1 class="text-[25px] leading-tight font-bold">
                Tugas Saya
            </h1>

            <p class="mt-1 text-sm text-slate-600">
                Diurutkan dari tenggat terdekat
            </p>
        </header>

        <main class="px-[18px] pt-5">

            {{-- FILTER TUGAS --}}
            <div class="flex gap-2.5" role="tablist" aria-label="Filter tugas">

                <button
                    id="tab-belum"
                    type="button"
                    role="tab"
                    aria-selected="true"
                    aria-controls="panel-belum"
                    class="rounded-full border border-brand bg-brand px-[18px] py-3 text-sm font-semibold text-white"
                >
                    Belum selesai
                </button>

                <button
                    id="tab-sudah"
                    type="button"
                    role="tab"
                    aria-selected="false"
                    aria-controls="panel-sudah"
                    class="rounded-full border border-slate-300 bg-white px-[18px] py-3 text-sm font-semibold text-slate-900"
                >
                    Sudah selesai
                </button>

            </div>

            {{-- TUGAS BELUM SELESAI --}}
            <div id="panel-belum" role="tabpanel" aria-labelledby="tab-belum"
                class="mt-4 space-y-4">

                {{-- BE SOK --}}
                <section>
                    <h2 class="mb-4 text-sm font-semibold text-slate-600">
                        Besok
                    </h2>

                    <article class="flex items-start gap-3.5 rounded-2xl border border-slate-200 bg-white p-4">

                        <div class="flex size-[50px] shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                            <svg viewBox="0 0 24 24" class="size-6"
                                fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <rect x="5" y="4" width="14" height="17" rx="2"/>
                                <path d="M9 4.5V3h6v1.5"/>
                                <path d="m9 12 2 2 4-4"/>
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <h3 class="text-[15px] leading-5 font-bold">
                                Latihan Pecahan Senilai
                            </h3>

                            <p class="mt-2 text-sm leading-5 text-slate-600">
                                Matematika · Besok, 12.00 WIB
                            </p>

                            <span class="mt-1.5 inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                <span class="size-2 rounded-full bg-amber-700"></span>
                                Belum dikerjakan
                            </span>
                        </div>

                    </article>
                </section>

                {{-- MINGGU INI --}}
                <section>
                    <h2 class="mb-4 text-sm font-semibold text-slate-600">
                        Minggu ini
                    </h2>

                    <article class="flex items-start gap-3.5 rounded-2xl border border-slate-200 bg-white p-4">

                        <div class="flex size-[50px] shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                            <svg viewBox="0 0 24 24" class="size-6"
                                fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <rect x="5" y="4" width="14" height="17" rx="2"/>
                                <path d="M9 4.5V3h6v1.5"/>
                                <path d="m9 12 2 2 4-4"/>
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <h3 class="text-[15px] leading-5 font-bold">
                                Gambar Bagian Daun
                            </h3>

                            <p class="mt-2 text-sm leading-5 text-slate-600">
                                IPA · Kamis, 23 Okt, 23.59 WIB
                            </p>

                            <span class="mt-1.5 inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                <span class="size-2 rounded-full bg-amber-700"></span>
                                Belum dikerjakan
                            </span>
                        </div>

                    </article>
                </section>

                {{-- LEWAT TENGGAT --}}
                <section>
                    <h2 class="mb-4 text-sm font-semibold text-red-600">
                        Lewat tenggat
                    </h2>

                    <article class="flex items-start gap-3.5 rounded-2xl border border-slate-200 bg-white p-4">

                        <div class="flex size-[50px] shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-700">
                            <svg viewBox="0 0 24 24" class="size-6"
                                fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <path d="M12 7v14"/>
                                <path d="M12 7C9 4 5 4 3 5v14c3-1 6-1 9 2"/>
                                <path d="M12 7c3-3 7-3 9-2v14c-3-1-6-1-9 2"/>
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <h3 class="text-[15px] leading-5 font-bold">
                                Membaca Cerita Rakyat
                            </h3>

                            <p class="mt-2 text-sm leading-5 text-slate-600">
                                Bahasa Indonesia · lewat 2 hari
                            </p>

                            <span class="mt-1.5 inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                <span class="size-2 rounded-full bg-red-700"></span>
                                Terlambat
                            </span>
                        </div>

                    </article>
                </section>

            </div>

            {{-- TUGAS SUDAH SELESAI --}}
            <div id="panel-sudah" role="tabpanel" aria-labelledby="tab-sudah"
                class="mt-4 hidden space-y-4">

                <section>
                    <h2 class="mb-4 text-sm font-semibold text-slate-600">
                        Sudah dikumpulkan
                    </h2>

                    <article class="flex items-start gap-3.5 rounded-2xl border border-slate-200 bg-white p-4">
                        <div class="flex size-[50px] shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-700">
                            <svg viewBox="0 0 24 24" class="size-6"
                                fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <rect x="5" y="4" width="14" height="17" rx="2"/>
                                <path d="M9 4.5V3h6v1.5"/>
                                <path d="m9 12 2 2 4-4"/>
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <h3 class="text-[15px] leading-5 font-bold">
                                Contoh Tugas Selesai
                            </h3>

                            <p class="mt-2 text-sm text-slate-600">
                                Matematika · Sudah dikumpulkan
                            </p>

                            <span class="mt-2 inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                Selesai
                            </span>
                        </div>
                    </article>
                </section>

            </div>

        </main>
    </div>

    {{-- BOTTOM NAVIGATION --}}
    <nav aria-label="Navigasi utama"
        class="fixed inset-x-0 bottom-0 z-50 border-t border-slate-200 bg-white">
        <div class="mx-auto grid h-[66px] w-full max-w-[390px] grid-cols-5 px-1">

            <a href="{{ route('siswa.beranda') }}" aria-current="page"
                class="flex flex-col items-center justify-center gap-0.5 text-slate-600">
                <span class="flex h-8 w-12 items-center justify-center rounded-full bg-blue-100">
                    <svg viewBox="0 0 24 24" class="size-6"
                        fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m3 10 9-7 9 7"/>
                        <path d="M5 9v11h14V9"/>
                        <path d="M9 20v-7h6v7"/>
                    </svg>
                </span>
                <span class="text-[11px] font-semibold">Beranda</span>
            </a>

            <a href="{{ route('siswa.tugas') }}"
                class="flex flex-col items-center justify-center gap-0.5 text-brand">
                <svg viewBox="0 0 24 24" class="size-6"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="5" y="4" width="14" height="17" rx="2"/>
                    <path d="M9 4.5V3h6v1.5"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
                <span class="text-[11px] font-medium">Tugas</span>
            </a>

            <a href="#"
                class="flex flex-col items-center justify-center gap-0.5 text-slate-600">
                <svg viewBox="0 0 24 24" class="size-6"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/>
                </svg>
                <span class="text-[11px] font-medium">Nilai</span>
            </a>

            <a href="#"
                class="flex flex-col items-center justify-center gap-0.5 text-slate-600">
                <svg viewBox="0 0 24 24" class="size-6"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                    <path d="M10 21h4"/>
                </svg>
                <span class="text-[11px] font-medium">Notifikasi</span>
            </a>

            <a href="#"
                class="flex flex-col items-center justify-center gap-0.5 text-slate-600">
                <svg viewBox="0 0 24 24" class="size-6"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M5 21v-2a7 7 0 0 1 14 0v2"/>
                </svg>
                <span class="text-[11px] font-medium">Akun</span>
            </a>

        </div>
    </nav>


    {{-- INTERAKSI TAB --}}
    <script>
        const tabBelum = document.getElementById('tab-belum');
        const tabSudah = document.getElementById('tab-sudah');
        const panelBelum = document.getElementById('panel-belum');
        const panelSudah = document.getElementById('panel-sudah');

        function pilihTab(sudahSelesai) {
            tabBelum.setAttribute('aria-selected', String(!sudahSelesai));
            tabSudah.setAttribute('aria-selected', String(sudahSelesai));

            tabBelum.className = sudahSelesai
                ? 'rounded-full border border-slate-300 bg-white px-[18px] py-3 text-sm font-semibold text-slate-900'
                : 'rounded-full border border-brand bg-brand px-[18px] py-3 text-sm font-semibold text-white';

            tabSudah.className = sudahSelesai
                ? 'rounded-full border border-brand bg-brand px-[18px] py-3 text-sm font-semibold text-white'
                : 'rounded-full border border-slate-300 bg-white px-[18px] py-3 text-sm font-semibold text-slate-900';

            panelBelum.classList.toggle('hidden', sudahSelesai);
            panelSudah.classList.toggle('hidden', !sudahSelesai);
        }

        tabBelum.addEventListener('click', () => pilihTab(false));
        tabSudah.addEventListener('click', () => pilihTab(true));
    </script>

</body>
</html>
