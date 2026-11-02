{{-- M08 Tambah/Ubah akun — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', $user->exists ? 'Ubah Akun' : 'Tambah Akun')

@section('content')

    <form method="POST" action="{{ $user->exists ? route('admin.akun.update', $user) : route('admin.akun.store') }}"
        class="space-y-4 rounded-card border border-border bg-white p-4">
        @csrf
        @if ($user->exists)
            @method('PUT')
        @endif

        @php($role = old('role', $user->role?->value ?? 'siswa'))

        <fieldset>
            <legend class="mb-2 font-semibold">Peran</legend>
            <div class="flex gap-3">
                <label class="flex h-12 flex-1 items-center gap-2 rounded-control border border-border px-3">
                    <input type="radio" name="role" value="siswa" @checked($role === 'siswa')> Siswa
                </label>
                <label class="flex h-12 flex-1 items-center gap-2 rounded-control border border-border px-3">
                    <input type="radio" name="role" value="guru" @checked($role === 'guru')> Guru
                </label>
            </div>
        </fieldset>

        <div>
            <label for="name" class="mb-1 block font-semibold">Nama</label>
            <input id="name" name="name" value="{{ old('name', $user->name) }}" required
                class="h-12 w-full rounded-control border border-border px-3">
        </div>

        <div>
            <label for="username" class="mb-1 block font-semibold">NISN (siswa) / NIP atau username (guru)</label>
            <input id="username" name="username" value="{{ old('username', $user->username) }}" required
                class="h-12 w-full rounded-control border border-border px-3">
        </div>

        <div>
            <label for="rombel" class="mb-1 block font-semibold">Rombel (khusus siswa)</label>
            @php($selectedRombel = old('rombel', $user->rombel?->name))
            <select id="rombel" name="rombel" class="h-12 w-full rounded-control border border-border px-3">
                <option value="">Pilih rombel</option>
                @foreach ($rombels as $rombel)
                    <option value="{{ $rombel->name }}" @selected($selectedRombel === $rombel->name)>{{ $rombel->name }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-caption text-text-muted">Rombel belum ada? Tambahkan dulu di menu Rombel.</p>
        </div>

        @unless ($user->exists)
            <p class="text-caption text-text-muted">Kata sandi awal dibuat otomatis dan ditampilkan setelah disimpan.</p>
        @endunless

        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('admin.akun.index') }}" class="flex h-12 items-center justify-center rounded-control border border-border">Batal</a>
            <button type="submit" class="h-12 rounded-control bg-brand font-semibold text-white">Simpan</button>
        </div>
    </form>

@endsection
