{{-- S14 Akun & ganti kata sandi — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Akun Saya')

@section('content')

    <a href="{{ route($user->homeRoute()) }}" class="inline-flex h-12 items-center text-brand underline">← Beranda</a>

    <section class="rounded-card border border-border bg-white p-4">
        <h1 class="text-h1 font-bold">{{ $user->name }}</h1>
        <dl class="mt-3 space-y-2">
            <div>
                <dt class="text-caption text-text-muted">{{ $user->isSiswa() ? 'NISN' : 'Username / NIP' }}</dt>
                <dd class="font-semibold">{{ $user->username }}</dd>
            </div>
            <div>
                <dt class="text-caption text-text-muted">Peran</dt>
                <dd class="font-semibold">{{ $user->role->label() }}</dd>
            </div>
            @if ($user->rombel)
                <div>
                    <dt class="text-caption text-text-muted">Rombel</dt>
                    <dd class="font-semibold">{{ $user->rombel->name }}</dd>
                </div>
            @endif
        </dl>
        <p class="mt-3 text-caption text-text-muted">Data salah? Minta bantuan wali kelas atau operator sekolah.</p>
    </section>

    {{-- FR-03: ganti kata sandi sendiri --}}
    <form method="POST" action="{{ route('akun.password') }}" class="space-y-3 rounded-card border border-border bg-white p-4">
        @csrf
        @method('PUT')

        <h2 class="font-bold">Ganti kata sandi</h2>

        <div>
            <label for="current_password" class="mb-1 block font-semibold">Kata sandi lama</label>
            <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                class="h-12 w-full rounded-control border border-border px-3">
        </div>

        <div>
            <label for="password" class="mb-1 block font-semibold">Kata sandi baru</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="Minimal 8 karakter"
                class="h-12 w-full rounded-control border border-border px-3">
        </div>

        <div>
            <label for="password_confirmation" class="mb-1 block font-semibold">Ulangi kata sandi baru</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                class="h-12 w-full rounded-control border border-border px-3">
        </div>

        <button type="submit" class="h-12 w-full rounded-control bg-brand font-semibold text-white">Simpan Kata Sandi</button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="h-12 w-full rounded-control border border-red-300 font-semibold text-danger">Keluar dari Akun</button>
    </form>

@endsection
