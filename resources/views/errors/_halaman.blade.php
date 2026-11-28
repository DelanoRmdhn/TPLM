{{-- Halaman error yang ramah anak (403, 404): ikon besar, pesan singkat, tombol kembali. --}}
@extends('layouts.app')

@section('title', $title)

@section('content')

    <x-card class="mt-6">
        <x-empty-state :icon="$icon" :title="$message">{{ $hint ?? '' }}</x-empty-state>
    </x-card>

    @auth
        <x-button :href="route(auth()->user()->homeRoute())" icon="house" size="lg" block>Kembali ke Beranda</x-button>
    @else
        <x-button :href="route('login')" icon="sign-in" size="lg" block>Kembali ke Login</x-button>
    @endauth

@endsection
