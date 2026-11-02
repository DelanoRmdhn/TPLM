
@extends('layouts.sementara')

@section('title', 'Akun Saya')
@section('standalone', true)

@section('content')

<div class="mx-auto min-h-screen w-full max-w-[390px] bg-slate-50 pb-24">

    {{-- HEADER --}}
    <header class="border-b border-slate-200 bg-white px-4 py-5">
        <h1 class="text-2xl leading-tight font-bold text-slate-900">
            Akun Saya
        </h1>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="mx-auto w-full max-w-md space-y-4 px-4 pt-5 pb-28">

        {{-- PROFILE CARD --}}
        <section class="rounded-2xl border border-[#DCE5F0] bg-white px-4 py-5 text-center">

            <div class="mx-auto flex h-[72px] w-[72px] items-center justify-center rounded-full bg-[#DCEAFF] text-[20px] font-bold text-[#2457E8]">
                {{ collect(explode(' ', $user->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->implode('') }}
            </div>

            <h2 class="mt-2 text-[20px] font-bold">
                {{ $user->name }}
            </h2>

            <p class="mt-1 text-[16px] text-[#475569]">
                {{ $user->isSiswa() ? 'NISN' : 'Username / NIP' }}
                {{ $user->username }}
            </p>

            @if ($user->rombel)
                <p class="mt-1 text-[14px] text-[#526078]">
                    {{ $user->rombel->name }} · SDN 002 Karangmulya
                </p>
            @endif

        </section>


        {{-- CHANGE PASSWORD CARD --}}
        <form method="POST"
            action="{{ route('akun.password') }}"
            class="space-y-4 rounded-2xl border border-[#DCE5F0] bg-white p-5">

            @csrf
            @method('PUT')

            <h2 class="mb-1 text-[18px] font-bold">
                Ganti Kata Sandi
            </h2>

            {{-- OLD PASSWORD --}}
            <div>
                <label for="current_password"
                    class="mb-2 block text-[16px] font-semibold">
                    Kata Sandi Lama
                </label>

                <input
                    id="current_password"
                    name="current_password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan kata sandi lama"
                    class="h-[52px] w-full rounded-xl border border-[#C9D5E5] bg-white px-4 text-[16px] text-[#172033] outline-none transition focus:border-[#2457FF] focus:ring-1 focus:ring-[#2457FF]"
                >
            </div>

            {{-- NEW PASSWORD --}}
            <div>
                <label for="password"
                    class="mb-2 block text-[16px] font-semibold">
                    Kata Sandi Baru
                </label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    minlength="6"
                    autocomplete="new-password"
                    placeholder="Ketik kata sandi baru"
                    class="h-[52px] w-full rounded-xl border border-[#C9D5E5] bg-white px-4 text-[16px] text-[#172033] outline-none transition focus:border-[#2457FF] focus:ring-1 focus:ring-[#2457FF]"
                >

                <p class="mt-2 flex items-center gap-1.5 text-[14px] text-[#526078]">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        width="17" height="17" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4m0-4h.01"/>
                    </svg>
                    Minimal 6 huruf atau angka.
                </p>
            </div>

            {{-- CONFIRM PASSWORD --}}
            <div>
                <label for="password_confirmation"
                    class="mb-2 block text-[16px] font-semibold">
                    Ulangi Kata Sandi Baru
                </label>

                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Ketik ulang kata sandi baru"
                    class="h-[52px] w-full rounded-xl border border-[#C9D5E5] bg-white px-4 text-[16px] text-[#172033] outline-none transition focus:border-[#2457FF] focus:ring-1 focus:ring-[#2457FF]"
                >
            </div>

            {{-- SHOW PASSWORD --}}
            <label class="flex cursor-pointer items-center gap-3 pt-1 text-[16px]">
                <input
                    id="showPassword"
                    type="checkbox"
                    class="h-6 w-6 rounded-md border-2 border-[#C9D5E5] accent-[#2457FF]"
                >
                <span>Tampilkan kata sandi</span>
            </label>

            {{-- SAVE BUTTON --}}
            <button type="submit"
                class="h-[48px] w-full rounded-xl bg-[#2457FF] font-semibold text-white transition hover:bg-blue-700">
                Simpan Kata Sandi
            </button>

        </form>


        {{-- INSTALL APPLICATION CARD --}}
        <section class="rounded-2xl border border-[#DCE5F0] bg-white p-4">

            <div class="flex items-start gap-3">

                <svg xmlns="http://www.w3.org/2000/svg"
                    width="20" height="20" viewBox="0 0 24 24"
                    fill="none" stroke="#2457FF"
                    stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="mt-1 shrink-0">
                    <rect x="6" y="2" width="12" height="20" rx="2"/>
                    <path d="M11 18h2"/>
                </svg>

                <p class="text-[16px] leading-[24px]">
                    Pasang aplikasi ini di layar utama HP supaya mudah dibuka seperti aplikasi lain.
                </p>

            </div>

            <button
                type="button"
                id="installApp"
                class="mt-3 flex h-[46px] w-full items-center justify-center gap-2 rounded-xl border border-[#2457FF] font-semibold text-[#2457FF] transition hover:bg-blue-50">

                <svg xmlns="http://www.w3.org/2000/svg"
                    width="19" height="19" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <rect x="6" y="2" width="12" height="20" rx="2"/>
                    <path d="M11 18h2"/>
                </svg>

                Pasang Aplikasi
            </button>

        </section>


        {{-- LOGOUT --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit"
                class="flex h-[50px] w-full items-center justify-center gap-2 rounded-xl border border-red-600 bg-[#FEE2E2] font-semibold text-red-600 transition hover:bg-red-100">

                <svg xmlns="http://www.w3.org/2000/svg"
                    width="20" height="20" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" x2="9" y1="12" y2="12"/>
                </svg>

                Keluar dari Akun
            </button>
        </form>

    </main>


    {{-- BOTTOM NAVIGATION --}}

{{-- BOTTOM NAVIGATION --}}
<x-bottom-navigation active="akun" />


</div>


{{-- JAVASCRIPT --}}
<script>
    // Tampilkan atau sembunyikan semua input password
    const showPassword = document.getElementById('showPassword');

    showPassword.addEventListener('change', function () {
        const passwordInputs = [
            document.getElementById('current_password'),
            document.getElementById('password'),
            document.getElementById('password_confirmation')
        ];

        passwordInputs.forEach(input => {
            input.type = this.checked ? 'text' : 'password';
        });
    });


    // // Instalasi PWA
    // let deferredPrompt;

    // window.addEventListener('beforeinstallprompt', (event) => {
    //     event.preventDefault();
    //     deferredPrompt = event;
    // });

    // document.getElementById('installApp').addEventListener('click', async () => {
    //     if (!deferredPrompt) {
    //         alert('Aplikasi belum tersedia untuk dipasang. Gunakan menu browser untuk menambahkan ke layar utama.');
    //         return;
    //     }

    //     deferredPrompt.prompt();
    //     await deferredPrompt.userChoice;
    //     deferredPrompt = null;
    // });
</script>

@endsection
