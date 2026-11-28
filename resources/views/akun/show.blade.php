{{-- S14 Akun saya, ganti kata sandi (FR-03), dan keluar (semua peran). --}}
@extends('layouts.app')

@section('title', 'Akun Saya')

@section('content')

    <x-card class="text-center">
        <span class="mx-auto flex size-20 items-center justify-center rounded-full bg-brand text-display font-extrabold text-white">
            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
        </span>
        <h1 class="mt-3 text-h1 font-extrabold">{{ $user->name }}</h1>
        <div class="mt-2 flex flex-wrap justify-center gap-2">
            <x-chip :icon="$user->isSiswa() ? 'student' : ($user->isGuru() ? 'chalkboard-teacher' : 'shield-star')" tone="brand">{{ $user->role->label() }}</x-chip>
            @if ($user->rombel)
                <x-chip icon="users-three">Kelas {{ $user->rombel->name }}</x-chip>
            @endif
        </div>

        <dl class="mt-4 rounded-control bg-background p-3 text-left">
            <dt class="flex items-center gap-1.5 text-caption font-bold text-text-muted">
                <x-icon name="identification-card" class="size-4" />
                {{ $user->isSiswa() ? 'NISN' : 'Username / NIP' }}
            </dt>
            <dd class="mt-0.5 text-h2 font-extrabold tracking-wide">{{ $user->username }}</dd>
        </dl>
        <p class="mt-3 text-caption text-text-muted">Data salah? Minta bantuan wali kelas atau operator sekolah.</p>
    </x-card>

    {{-- FR-03: ganti kata sandi sendiri --}}
    <form method="POST" action="{{ route('akun.password') }}" class="space-y-4 rounded-card bg-surface p-5 shadow-card">
        @csrf
        @method('PUT')

        <h2 class="flex items-center gap-2 text-h2 font-extrabold">
            <x-icon name="key" class="size-6 text-brand" />
            Ganti kata sandi
        </h2>

        <x-text-input name="current_password" type="password" label="Kata sandi lama" required autocomplete="current-password" />
        <x-text-input name="password" type="password" label="Kata sandi baru" hint="Minimal 8 karakter." required autocomplete="new-password" />
        <x-text-input name="password_confirmation" type="password" label="Ulangi kata sandi baru" required autocomplete="new-password" />

        <x-button icon="floppy-disk" block>Simpan Kata Sandi</x-button>
    </form>

    <form method="POST" action="{{ route('logout') }}" data-confirm-title="Keluar dari akun?" data-confirm="Kamu perlu masuk lagi dengan NISN/username dan kata sandi." data-confirm-label="Ya, keluar">
        @csrf
        <x-button variant="ghost" icon="sign-out" block class="text-danger hover:bg-danger-50">Keluar dari Akun</x-button>
    </form>

@endsection
