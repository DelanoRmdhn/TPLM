{{-- Halaman error yang ramah anak — SEMENTARA dari BE. Nanti bisa diganti Delano. --}}
@extends('layouts.sementara')

@section('title', $title)

@section('content')

    <section class="rounded-card border border-border bg-white p-6 text-center">
        <p class="text-5xl" aria-hidden="true">{{ $icon }}</p>
        <h1 class="mt-3 text-h1 font-bold">{{ $message }}</h1>
        @isset($hint)
            <p class="mt-2 text-text-muted">{{ $hint }}</p>
        @endisset
    </section>

    @auth
        <a href="{{ route(auth()->user()->homeRoute()) }}" class="flex h-12 items-center justify-center rounded-control bg-brand font-semibold text-white">Kembali ke Beranda</a>
    @else
        <a href="{{ route('login') }}" class="flex h-12 items-center justify-center rounded-control bg-brand font-semibold text-white">Kembali ke Login</a>
    @endauth

@endsection
