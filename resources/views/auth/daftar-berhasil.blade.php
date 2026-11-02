{{-- M01e Daftar berhasil — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Pendaftaran Berhasil')

@section('content')

    @if ($account['pending'] ?? false)
        {{-- Guru: menunggu diaktifkan admin --}}
        <section class="rounded-card border border-amber-300 bg-amber-50 p-4 text-center">
            <h1 class="text-h1 font-bold text-amber-800">Pendaftaran terkirim</h1>
            <p class="mt-2">Halo, {{ $account['name'] }}. Akun kamu menunggu persetujuan admin sekolah.</p>
            <p class="mt-2 text-text-muted">Setelah disetujui, masuk dengan:</p>
            <p class="font-mono text-h1 font-bold">{{ $account['username'] }}</p>
        </section>

        <a href="{{ route('login') }}" class="flex h-12 items-center justify-center rounded-control border border-brand font-semibold text-brand">Kembali ke Login</a>
    @else
        <section class="rounded-card border border-green-200 bg-green-50 p-4 text-center">
            <h1 class="text-h1 font-bold text-green-800">Pendaftaran berhasil!</h1>
            <p class="mt-2">Halo, {{ $account['name'] }}. Akun kamu sudah aktif.</p>
            <p class="mt-2 text-text-muted">Masuk dengan:</p>
            <p class="font-mono text-h1 font-bold">{{ $account['username'] }}</p>
        </section>

        <a href="{{ route('login') }}" class="flex h-12 items-center justify-center rounded-control bg-brand font-semibold text-white">Masuk Sekarang</a>
    @endif

@endsection
