{{-- M01c Buat kata sandi baru: wajib saat login pertama atau setelah direset admin (PB-02, PB-04). --}}
@extends('layouts.guest')

@section('title', 'Buat Kata Sandi Baru')
@section('heading', 'Buat Kata Sandi Baru')
@section('subheading', 'Halo, '.auth()->user()->name.'. Ganti kata sandi awal kamu dulu sebelum mulai.')

@section('content')

    <form action="{{ route('password.store') }}" method="POST" class="space-y-4 rounded-card bg-surface p-5 shadow-card">
        @csrf

        <p class="flex items-start gap-3 rounded-control bg-sunny-50 p-4 text-caption text-[#8a5a00]">
            <x-icon name="lock-key" variant="fill" class="size-6" />
            <span>Pilih kata sandi yang mudah kamu ingat tapi sulit ditebak teman. Jangan beri tahu siapa pun.</span>
        </p>

        <x-password-input name="password" label="Kata sandi baru" placeholder="Minimal 8 karakter" required autocomplete="new-password" />
        <x-password-input name="password_confirmation" label="Ulangi kata sandi baru" placeholder="Ketik ulang kata sandi" required autocomplete="new-password" />

        <x-button icon="check-circle" size="lg" block>Simpan Kata Sandi</x-button>
    </form>

    <form action="{{ route('logout') }}" method="POST" class="mt-3">
        @csrf
        <x-button variant="ghost" icon="sign-out" block>Keluar</x-button>
    </form>

@endsection
