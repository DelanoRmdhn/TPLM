{{-- M01 Login (FR-01): masuk dengan NISN/username + kata sandi, tanpa email. --}}
@extends('layouts.guest')

@section('title', 'Masuk')
@section('heading', 'Selamat datang!')
@section('subheading', 'Masuk untuk melihat tugas dan nilaimu.')
@section('inline-status', true)

@section('content')

    {{-- Info dari server, mis. "Pendaftaran akun sedang ditutup." atau "Sesi kamu sudah berakhir." --}}
    @if (session('status'))
        <p role="status" class="mb-4 flex items-start gap-2 rounded-control bg-brand-50 p-4 font-semibold text-brand-800">
            <x-icon name="info" variant="fill" class="mt-0.5 size-5" />
            {{ session('status') }}
        </p>
    @endif

    <form action="{{ route('login.store') }}" method="POST" class="space-y-4 rounded-card bg-surface p-5 shadow-card">
        @csrf

        <x-text-input name="username" label="NISN atau Username" placeholder="Contoh: 0123984101" autocomplete="username" required />
        <x-password-input name="password" label="Kata sandi" placeholder="Masukkan kata sandi" autocomplete="current-password" required />

        <x-button icon="sign-in" size="lg" block>Masuk</x-button>
    </form>

    {{-- Daftar: hanya tampil saat pendaftaran dibuka (PB-30, M01f) --}}
    @if ($registrationOpen)
        <div class="mt-4 text-center">
            <p class="text-text-muted">Belum punya akun?</p>
            <x-button :href="route('register')" variant="secondary" icon="user-plus" block class="mt-2">Daftar Akun Baru</x-button>
        </div>
    @endif

    <p class="mt-4 flex items-start gap-3 rounded-control bg-surface/70 p-4 text-caption">
        <x-icon name="lifebuoy" class="size-6 text-brand" />
        <span>Lupa kata sandi? Minta bantuan wali kelas atau operator sekolah. Masuk memakai NISN, tanpa email.</span>
    </p>

    <div class="mt-3">
        <x-button href="#" variant="ghost" icon="device-mobile" block data-install-app>Pasang Aplikasi di HP</x-button>
        <p data-install-help class="mt-1 hidden text-center text-caption text-text-muted">
            Buka menu browser (tombol titik tiga atau Bagikan), lalu pilih "Tambahkan ke Layar Utama".
        </p>
    </div>

@endsection
