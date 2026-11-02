{{-- Kelola rombel — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Rombel')

@section('content')

    <a href="{{ route('admin.akun.index') }}" class="inline-flex h-12 items-center rounded-control border border-border px-4">← Kelola Akun</a>

    {{-- Tambah rombel --}}
    <form method="POST" action="{{ route('admin.rombel.store') }}" class="space-y-3 rounded-card border border-border bg-white p-4">
        @csrf
        <label for="name" class="block font-semibold">Tambah rombel</label>
        <div class="flex gap-2">
            <input id="name" name="name" value="{{ old('name') }}" required maxlength="20" placeholder="Contoh: 4C"
                class="h-12 min-w-0 flex-1 rounded-control border border-border px-3">
            <button type="submit" class="h-12 shrink-0 rounded-control bg-brand px-4 font-semibold text-white">Tambah</button>
        </div>
        <p class="text-caption text-text-muted">Format: angka kelas lalu huruf, tanpa strip (4A, bukan 4-A). Sama dengan isi kolom Rombel di template impor.</p>
    </form>

    {{-- Daftar rombel --}}
    <section class="rounded-card border border-border bg-white p-4">
        <h2 class="font-bold">Daftar rombel ({{ $rombels->count() }})</h2>
        <p class="mt-1 text-caption text-text-muted">Pilihan ini dipakai di impor Excel, form akun, pendaftaran, dan form buat kelas.</p>

        <ul class="mt-3 divide-y divide-border">
            @forelse ($rombels as $rombel)
                @php($used = $rombel->students_count > 0 || $rombel->classrooms_count > 0)
                <li class="space-y-2 py-3">
                    <form method="POST" action="{{ route('admin.rombel.update', $rombel) }}" class="flex gap-2">
                        @csrf
                        @method('PUT')
                        <label for="rombel-{{ $rombel->id }}" class="sr-only">Nama rombel</label>
                        <input id="rombel-{{ $rombel->id }}" name="name" value="{{ $rombel->name }}" required maxlength="20"
                            class="h-12 min-w-0 flex-1 rounded-control border border-border px-3">
                        <button type="submit" class="h-12 shrink-0 rounded-control border border-brand px-4 font-semibold text-brand">Simpan</button>
                    </form>

                    <div class="flex items-center justify-between gap-2">
                        <span class="text-caption text-text-muted">{{ $rombel->students_count }} siswa · {{ $rombel->classrooms_count }} kelas</span>
                        @unless ($used)
                            <form method="POST" action="{{ route('admin.rombel.destroy', $rombel) }}"
                                onsubmit="return confirm(@js('Hapus rombel '.$rombel->name.'?'))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="h-12 rounded-control border border-red-300 px-4 font-semibold text-red-700">Hapus</button>
                            </form>
                        @endunless
                    </div>
                </li>
            @empty
                <li class="py-3 text-text-muted">Belum ada rombel. Tambahkan dulu sebelum impor siswa.</li>
            @endforelse
        </ul>
    </section>

@endsection
