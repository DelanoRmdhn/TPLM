{{-- M01e Daftar berhasil — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Pendaftaran Berhasil')

@section('content')

    <section class="rounded-card border border-green-200 bg-green-50 p-4 text-center">
        <h1 class="text-h1 font-bold text-green-800">Pendaftaran berhasil!</h1>
        <p class="mt-2">Halo, {{ $account['name'] }}. Akun kamu sudah aktif.</p>
        <p class="mt-2 text-text-muted">Masuk dengan:</p>
        <p class="font-mono text-h1 font-bold">{{ $account['username'] }}</p>
    </section>

    <a href="{{ route('login') }}" class="flex h-12 items-center justify-center rounded-control bg-brand font-semibold text-white">Masuk Sekarang</a>

@endsection
