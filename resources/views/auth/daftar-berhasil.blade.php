{{-- M01e Daftar berhasil. Siswa langsung aktif, guru menunggu persetujuan admin. --}}
@extends('layouts.guest')

@php($pending = $account['pending'] ?? false)

@section('title', 'Pendaftaran Berhasil')
@section('heading', $pending ? 'Pendaftaran terkirim' : 'Pendaftaran berhasil!')

@section('content')

    <x-card class="text-center">
        <span @class([
            'mx-auto flex size-20 items-center justify-center rounded-full motion-safe:animate-pop',
            'bg-sunny-50 text-warning' => $pending,
            'bg-success-50 text-success' => ! $pending,
        ])>
            <x-icon :name="$pending ? 'hourglass-medium' : 'seal-check'" variant="fill" class="size-11" />
        </span>

        <p class="mt-4">
            Halo, <strong>{{ $account['name'] }}</strong>.
            {{ $pending ? 'Akun kamu menunggu persetujuan admin sekolah.' : 'Akun kamu sudah aktif.' }}
        </p>

        <div class="mt-4 rounded-control bg-background p-4">
            <p class="text-caption font-bold text-text-muted">{{ $pending ? 'Setelah disetujui, masuk dengan' : 'Masuk dengan' }}</p>
            <p class="mt-1 text-h1 font-extrabold tracking-wide">{{ $account['username'] }}</p>
        </div>
    </x-card>

    @if ($pending)
        <x-button :href="route('login')" variant="secondary" icon="arrow-left" block class="mt-4">Kembali ke Login</x-button>
    @else
        <x-button :href="route('login')" icon="sign-in" size="lg" block class="mt-4">Masuk Sekarang</x-button>
    @endif

@endsection
