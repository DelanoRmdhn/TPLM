{{-- M08 Kelola akun — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Kelola Akun')

@section('content')

    {{-- M08c: kata sandi sementara (tampil sekali) --}}
    @if ($temp = session('temporary_password'))
        <section class="rounded-card border border-amber-300 bg-amber-50 p-4">
            <h2 class="font-bold">{{ $temp['title'] }}</h2>
            <p class="mt-1">{{ $temp['name'] }} · {{ $temp['username'] }}</p>
            <p class="mt-2 text-caption text-text-muted">Kata sandi sementara:</p>
            <p class="font-mono text-h1 font-bold tracking-wider">{{ $temp['password'] }}</p>
            <p class="mt-2 text-caption text-text-muted">
                Catat dan berikan ke pemilik akun. Kata sandi ini hanya tampil sekali dan wajib diganti saat login.
            </p>
        </section>
    @endif

    {{-- M08e: buka/tutup pendaftaran --}}
    <section class="flex flex-wrap items-center justify-between gap-3 rounded-card border border-border bg-white p-4">
        <div>
            <p class="font-semibold">Pendaftaran akun mandiri</p>
            <p class="{{ $registrationOpen ? 'text-green-700' : 'text-text-muted' }}">
                Status: {{ $registrationOpen ? 'Dibuka' : 'Ditutup' }}
            </p>
        </div>
        <form method="POST" action="{{ route('admin.pendaftaran.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="is_open" value="{{ $registrationOpen ? 0 : 1 }}">
            <button type="submit" class="h-12 rounded-control px-4 font-semibold text-white {{ $registrationOpen ? 'bg-danger' : 'bg-brand' }}">
                {{ $registrationOpen ? 'Tutup Pendaftaran' : 'Buka Pendaftaran' }}
            </button>
        </form>
    </section>

    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('admin.akun.create') }}" class="flex h-12 items-center justify-center rounded-control bg-brand font-semibold text-white">+ Tambah Akun</a>
        <a href="{{ route('admin.impor.index') }}" class="flex h-12 items-center justify-center rounded-control border border-brand font-semibold text-brand">Impor Excel</a>
        <a href="{{ route('admin.mapel.index') }}" class="col-span-2 flex h-12 items-center justify-center rounded-control border border-border font-semibold">Mata Pelajaran</a>
    </div>

    {{-- Filter --}}
    <form method="GET" class="grid grid-cols-2 gap-2 rounded-card border border-border bg-white p-4 sm:grid-cols-4">
        <input name="q" value="{{ request('q') }}" placeholder="Cari nama / NISN / NIP"
            class="col-span-2 h-12 rounded-control border border-border px-3 sm:col-span-4">
        <select name="peran" class="h-12 rounded-control border border-border px-2">
            <option value="">Semua peran</option>
            <option value="siswa" @selected(request('peran') === 'siswa')>Siswa</option>
            <option value="guru" @selected(request('peran') === 'guru')>Guru</option>
        </select>
        <select name="rombel" class="h-12 rounded-control border border-border px-2">
            <option value="">Semua rombel</option>
            @foreach ($rombels as $rombel)
                <option value="{{ $rombel->id }}" @selected(request('rombel') == $rombel->id)>{{ $rombel->name }}</option>
            @endforeach
        </select>
        <select name="status" class="h-12 rounded-control border border-border px-2">
            <option value="">Semua status</option>
            <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
        </select>
        <button type="submit" class="h-12 rounded-control bg-text font-semibold text-white">Terapkan</button>
    </form>

    {{-- Daftar akun --}}
    <section class="space-y-3">
        <p class="text-caption text-text-muted">{{ $users->total() }} akun</p>

        @forelse ($users as $user)
            <article class="rounded-card border border-border bg-white p-4 {{ $user->is_active ? '' : 'opacity-60' }}">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="font-semibold">{{ $user->name }}</p>
                        <p class="text-caption text-text-muted">
                            {{ $user->role->label() }} · {{ $user->username }}
                            @if ($user->rombel) · {{ $user->rombel->name }} @endif
                        </p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-caption {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-slate-200 text-slate-700' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                    <a href="{{ route('admin.akun.edit', $user) }}" class="flex h-12 items-center rounded-control border border-border px-4">Ubah</a>

                    <form method="POST" action="{{ route('admin.akun.reset-password', $user) }}"
                        onsubmit="return confirm(@js('Reset kata sandi '.$user->name.'?'))">
                        @csrf
                        <button type="submit" class="h-12 rounded-control border border-border px-4">Reset Kata Sandi</button>
                    </form>

                    @if ($user->is_active)
                        {{-- M08d: konfirmasi nonaktifkan --}}
                        <form method="POST" action="{{ route('admin.akun.deactivate', $user) }}"
                            onsubmit="return confirm(@js('Nonaktifkan akun '.$user->name.'? Akun tidak bisa login, tapi datanya tetap tersimpan.'))">
                            @csrf
                            <button type="submit" class="h-12 rounded-control border border-red-300 px-4 text-danger">Nonaktifkan</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.akun.activate', $user) }}">
                            @csrf
                            <button type="submit" class="h-12 rounded-control border border-green-300 px-4 text-green-700">Aktifkan</button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <p class="rounded-card border border-border bg-white p-4 text-text-muted">Belum ada akun yang cocok.</p>
        @endforelse

        {{ $users->links() }}
    </section>

@endsection
