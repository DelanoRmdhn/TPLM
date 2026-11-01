{{-- M01c: SEMENTARA (dari BE). Nanti diganti hasil slicing Delano. --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Buat Kata Sandi Baru — SDN 002 Karangmulya</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-background font-sans text-text antialiased">

    <main class="mx-auto flex justify-center min-h-screen w-full max-w-90 flex-col px-2.5 py-6">

        <header class="mb-3 text-center">
            <h1 class="text-[16px] leading-[1.3] font-bold">Buat Kata Sandi Baru</h1>
            <p class="mt-1 text-[10px] leading-[1.4] text-text-muted">
                Halo, {{ auth()->user()->name }}. Ganti kata sandi awal kamu dulu sebelum mulai.
            </p>
        </header>

        <section class="rounded-card border border-border bg-white p-3">
            <form action="{{ route('password.store') }}" method="POST" class="space-y-3">
                @csrf

                @if ($errors->any())
                    <div role="alert" class="rounded-control border border-red-200 bg-red-50 px-2.5 py-2 text-[10px] leading-[1.4] text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div>
                    <label for="password" class="mb-1.5 block text-[10px] leading-[1.3] font-semibold">
                        Kata Sandi Baru
                    </label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                        placeholder="Minimal 8 karakter"
                        class="h-8 w-full rounded-control border border-border bg-white px-2.5 text-[10px] outline-none placeholder:text-slate-500 focus:border-brand focus:ring-1 focus:ring-brand">
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-[10px] leading-[1.3] font-semibold">
                        Ulangi Kata Sandi Baru
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                        placeholder="Ketik ulang kata sandi"
                        class="h-8 w-full rounded-control border border-border bg-white px-2.5 text-[10px] outline-none placeholder:text-slate-500 focus:border-brand focus:ring-1 focus:ring-brand">
                </div>

                <button type="submit"
                    class="flex h-8 w-full items-center justify-center rounded-control bg-brand text-[11px] font-semibold text-white transition hover:bg-blue-700">
                    Simpan Kata Sandi
                </button>
            </form>
        </section>

        <form action="{{ route('logout') }}" method="POST" class="mt-3 text-center">
            @csrf
            <button type="submit" class="text-[10px] text-text-muted underline">Keluar</button>
        </form>

    </main>

</body>
</html>