{{-- Kelola mata pelajaran — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Mata Pelajaran')

@section('content')

    <a href="{{ route('admin.akun.index') }}" class="inline-flex h-12 items-center rounded-control border border-border px-4">← Kelola Akun</a>

    {{-- Tambah mapel --}}
    <form method="POST" action="{{ route('admin.mapel.store') }}" class="space-y-3 rounded-card border border-border bg-white p-4">
        @csrf
        <label for="name" class="block font-semibold">Tambah mata pelajaran</label>
        <div class="flex gap-2">
            <input id="name" name="name" value="{{ old('name') }}" required maxlength="100" placeholder="Contoh: Bahasa Sunda"
                class="h-12 min-w-0 flex-1 rounded-control border border-border px-3">
            <button type="submit" class="h-12 shrink-0 rounded-control bg-brand px-4 font-semibold text-white">Tambah</button>
        </div>
    </form>

    {{-- Daftar mapel --}}
    <section class="rounded-card border border-border bg-white p-4">
        <h2 class="font-bold">Daftar mata pelajaran ({{ $subjects->count() }})</h2>
        <p class="mt-1 text-caption text-text-muted">Pilihan ini muncul di form buat kelas guru. Ganti nama juga mengganti nama mapel di kelas yang sudah ada.</p>

        <ul class="mt-3 divide-y divide-border">
            @forelse ($subjects as $subject)
                @php($used = (int) ($usage[$subject->name] ?? 0))
                <li class="space-y-2 py-3">
                    <form method="POST" action="{{ route('admin.mapel.update', $subject) }}" class="flex gap-2">
                        @csrf
                        @method('PUT')
                        <label for="mapel-{{ $subject->id }}" class="sr-only">Nama mapel</label>
                        <input id="mapel-{{ $subject->id }}" name="name" value="{{ $subject->name }}" required maxlength="100"
                            class="h-12 min-w-0 flex-1 rounded-control border border-border px-3">
                        <button type="submit" class="h-12 shrink-0 rounded-control border border-brand px-4 font-semibold text-brand">Simpan</button>
                    </form>

                    <div class="flex items-center justify-between gap-2">
                        <span class="text-caption text-text-muted">{{ $used > 0 ? "Dipakai di {$used} kelas" : 'Belum dipakai kelas' }}</span>
                        @if ($used === 0)
                            <form method="POST" action="{{ route('admin.mapel.destroy', $subject) }}"
                                onsubmit="return confirm(@js('Hapus mata pelajaran '.$subject->name.'?'))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="h-12 rounded-control border border-red-300 px-4 font-semibold text-red-700">Hapus</button>
                            </form>
                        @endif
                    </div>
                </li>
            @empty
                <li class="py-3 text-text-muted">Belum ada mata pelajaran. Tambahkan dulu supaya guru bisa membuat kelas.</li>
            @endforelse
        </ul>
    </section>

@endsection
