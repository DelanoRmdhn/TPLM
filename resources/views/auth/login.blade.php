
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login — SDN 002 Karangmulya</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-background font-sans text-text antialiased">

    <main class="mx-auto flex justify-center min-h-screen w-full max-w-90 flex-col px-2.5 py-6">

        {{-- Branding --}}
        <header class="mb-3 flex flex-col items-center text-center">

            {{-- Logo sementara: ikon SVG topi wisuda --}}
            <div class="flex size-11 items-center justify-center rounded-control bg-brand text-white">
                <svg
                    viewBox="0 0 24 24"
                    class="size-7"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="m2 9 10-5 10 5-10 5L2 9Z" />
                    <path d="M6 11v5c3.5 3 8.5 3 12 0v-5" />
                    <path d="M22 9v6" />
                </svg>
            </div>

            <h1 class="mt-1.5 text-[16px] leading-[1.3] font-bold">
                SDN 002 Karangmulya
            </h1>

            <p class="mt-1 text-[10px] leading-[1.4] text-text-muted">
                Belajar online untuk siswa dan guru
            </p>

        </header>

        {{-- Login Form --}}
        <section class="rounded-card border border-border bg-white p-3">

            <form action="#" method="POST" class="space-y-3">
                @csrf

                {{-- NISN / Username --}}
                <div>
                    <label
                        for="username"
                        class="mb-1.5 block text-[10px] leading-[1.3] font-semibold"
                    >
                        NISN atau Username
                    </label>

                    <input
                        id="username"
                        name="username"
                        type="text"
                        autocomplete="username"
                        placeholder="Contoh: 0123984101"
                        required
                        class="h-8 w-full rounded-control border border-border bg-white px-2.5 text-[10px] outline-none placeholder:text-slate-500 focus:border-brand focus:ring-1 focus:ring-brand"
                    >
                </div>

                {{-- Password --}}
                <div>
                    <label
                        for="password"
                        class="mb-1.5 block text-[10px] leading-[1.3] font-semibold"
                    >
                        Kata Sandi
                    </label>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        placeholder="Masukkan kata sandi"
                        required
                        class="h-8 w-full rounded-control border border-border bg-white px-2.5 text-[10px] outline-none placeholder:text-slate-500 focus:border-brand focus:ring-1 focus:ring-brand"
                    >
                </div>

                {{-- Toggle Password --}}
                <label class="flex items-center gap-2 text-[10px] leading-[1.4]">
                    <input
                        id="show-password"
                        type="checkbox"
                        class="size-3.5 accent-brand"
                    >
                    Tampilkan kata sandi
                </label>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="flex h-8 w-full items-center justify-center gap-1.5 rounded-control bg-brand text-[11px] font-semibold text-white transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="size-3.5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <rect x="5" y="10" width="14" height="11" rx="2" />
                        <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                    </svg>

                    Masuk
                </button>

            </form>

        </section>

        {{-- Register --}}
        <div class="mt-4 text-center">

            <p class="text-[10px] leading-[1.4] text-text-muted">
                Belum punya akun?
            </p>

            <a
                href="#"
                class="mt-1.5 flex h-8 w-full items-center justify-center gap-1.5 rounded-control border border-brand bg-white text-[11px] font-semibold text-brand transition hover:bg-blue-50"
            >
                <svg
                    viewBox="0 0 24 24"
                    class="size-3.5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <path d="M12 5v14M5 12h14" />
                </svg>

                Daftar Akun Baru
            </a>

        </div>

        {{-- Help Information --}}
        <aside class="mt-3 flex items-start gap-2 rounded-card bg-blue-100 px-2.5 py-2.5 text-[10px] leading-[1.5] text-text">

            <svg
                viewBox="0 0 24 24"
                class="mt-0.5 size-3.5 shrink-0 text-brand"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >
                <circle cx="12" cy="12" r="9" />
                <path d="M12 11v5M12 8h.01" />
            </svg>

            <p class="text-center">
                Lupa kata sandi? Minta bantuan Wali Kelas atau Operator Sekolah.
            </p>

        </aside>

        <div class="mt-4 text-center">
            <a
                href="#"
                class="mt-1.5 flex h-8 w-full items-center justify-center gap-1.5 rounded-control border border-brand bg-white text-[11px] font-semibold text-brand transition hover:bg-blue-50"
            >
                <svg
                    viewBox="0 0 24 24"
                    class="size-3.5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <path d="M12 5v14M5 12h14" />
                </svg>

                Pasang Aplikasi di HP
            </a>
        </div>

        <div class="mt-4 text-center">
            <span class="text-[10px] leading-[1.4] text-text-muted">Masuk memakai NISN tanpa email.</span>
        </div>


    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const showPassword = document.getElementById('show-password');

        showPassword.addEventListener('change', () => {
            passwordInput.type = showPassword.checked ? 'text' : 'password';
        });
    </script>

</body>
</html>
