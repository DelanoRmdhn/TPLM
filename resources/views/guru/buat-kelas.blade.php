
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Kelas Baru</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">

    <div class="mx-auto min-h-screen w-full max-w-[390px] bg-slate-50">

        {{-- HEADER --}}
        <header class="border-b border-slate-200 bg-white px-4 pb-3 pt-1">

            <a href="{{ route('guru.beranda') }}"
                class="inline-flex items-center gap-2 rounded-lg px-3 py-1 text-sm font-medium text-slate-800 hover:bg-slate-100">

                <svg viewBox="0 0 24 24" class="size-5"
                    fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">
                    <path d="m15 18-6-6 6-6"/>
                    <path d="M9 12h12"/>
                </svg>

                Kembali
            </a>

            <h1 class="mt-2 text-xl font-bold">
                Buat Kelas Baru
            </h1>

            <p class="mt-2 text-sm text-slate-600">
                Kelas berisi materi, tugas, dan nilai
            </p>

        </header>

        <main class="space-y-4 px-4 py-5">

            {{-- FORM KELAS --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5">

                <form action="#" method="POST" class="space-y-4">
                    @csrf

                    {{-- Nama Kelas --}}
                    <div>
                        <label for="nama_kelas"
                            class="mb-2 block text-base font-semibold">
                            Nama Kelas
                        </label>

                        <input
                            type="text"
                            id="nama_kelas"
                            name="nama_kelas"
                            value="Matematika 4-A"
                            placeholder="Masukkan nama kelas"
                            required
                            class="h-[50px] w-full rounded-xl border border-slate-300 px-4 text-sm outline-none focus:border-brand focus:ring-1 focus:ring-brand"
                        >
                    </div>

                    {{-- Mata Pelajaran --}}
                    <div>
                        <label for="mata_pelajaran"
                            class="mb-2 block text-base font-semibold">
                            Mata Pelajaran
                        </label>

                        <div class="relative">
                            <select
                                id="mata_pelajaran"
                                name="mata_pelajaran"
                                required
                                class="h-[50px] w-full appearance-none rounded-xl border border-slate-300 bg-white px-4 pr-10 text-sm outline-none focus:border-brand focus:ring-1 focus:ring-brand"
                            >
                                <option value="Matematika">Matematika</option>
                                <option value="IPA">IPA</option>
                                <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                                <option value="Pendidikan Pancasila">Pendidikan Pancasila</option>
                            </select>

                            <svg viewBox="0 0 24 24"
                                class="pointer-events-none absolute right-4 top-1/2 size-5 -translate-y-1/2 text-slate-600"
                                fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Rombel --}}
                    <div>
                        <label for="rombel"
                            class="mb-2 block text-base font-semibold">
                            Rombel
                        </label>

                        <div class="relative">
                            <select
                                id="rombel"
                                name="rombel"
                                required
                                class="h-[50px] w-full appearance-none rounded-xl border border-slate-300 bg-white px-4 pr-10 text-sm outline-none focus:border-brand focus:ring-1 focus:ring-brand"
                            >
                                <option value="4-A">4-A · 30 siswa</option>
                                <option value="4-B">4-B · 28 siswa</option>
                                <option value="4-C">4-C · 29 siswa</option>
                            </select>

                            <svg viewBox="0 0 24 24"
                                class="pointer-events-none absolute right-4 top-1/2 size-5 -translate-y-1/2 text-slate-600"
                                fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Tahun Ajaran --}}
                    <div>
                        <label for="tahun_ajaran"
                            class="mb-2 block text-base font-semibold">
                            Tahun Ajaran
                        </label>

                        <div class="relative">
                            <select
                                id="tahun_ajaran"
                                name="tahun_ajaran"
                                required
                                class="h-[50px] w-full appearance-none rounded-xl border border-slate-300 bg-white px-4 pr-10 text-sm outline-none focus:border-brand focus:ring-1 focus:ring-brand"
                            >
                                <option value="2026/2027">2026/2027</option>
                                <option value="2027/2028">2027/2028</option>
                            </select>

                            <svg viewBox="0 0 24 24"
                                class="pointer-events-none absolute right-4 top-1/2 size-5 -translate-y-1/2 text-slate-600"
                                fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </div>
                    </div>

                </form>

            </section>

            {{-- INFORMASI ROMBEL --}}
            <aside class="flex items-start gap-3 rounded-2xl bg-green-100 p-4 text-slate-800">

                <svg viewBox="0 0 24 24"
                    class="mt-0.5 size-5 shrink-0 text-green-700"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>

                <p class="text-sm leading-[1.6]">
                    Semua <span id="jumlah-siswa">30</span> siswa rombel
                    <span id="nama-rombel">4-A</span> otomatis masuk ke kelas ini.
                    Tidak perlu menambah satu per satu.
                </p>

            </aside>

            {{-- TOMBOL BUAT KELAS --}}
            <button
                type="button"
                onclick="alert('Tampilan sudah siap. Form ini belum terhubung ke penyimpanan kelas.')"
                class="flex h-[50px] w-full items-center justify-center gap-2 rounded-xl bg-brand text-sm font-semibold text-white transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
            >
                <svg viewBox="0 0 24 24" class="size-5"
                    fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">
                    <path d="m5 12 5 5L20 7"/>
                </svg>

                Buat Kelas
            </button>

            <p class="text-sm leading-[1.6] text-slate-600">
                Siswa pindahan bisa bergabung memakai kode kelas.
                Kode bisa diatur di Pengaturan Kelas.
            </p>

        </main>

    </div>

    <script>
        const rombel = document.getElementById('rombel');
        const jumlahSiswa = document.getElementById('jumlah-siswa');
        const namaRombel = document.getElementById('nama-rombel');

        const jumlahSiswaPerRombel = {
            '4-A': 30,
            '4-B': 28,
            '4-C': 29,
        };

        rombel.addEventListener('change', () => {
            const kelas = rombel.value;

            jumlahSiswa.textContent = jumlahSiswaPerRombel[kelas];
            namaRombel.textContent = kelas;
        });
    </script>

</body>
</html>
