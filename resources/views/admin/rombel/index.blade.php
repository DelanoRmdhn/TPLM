{{-- Kelola rombel (admin): tambah, ganti nama, hapus yang belum punya siswa/kelas. --}}
@extends('layouts.app')

@section('title', 'Rombel')
@section('back', route('admin.beranda'))
@section('back-label', 'Kembali ke Beranda')

@section('content')

    {{-- Tambah rombel --}}
    <form method="POST" action="{{ route('admin.rombel.store') }}" class="space-y-3 rounded-card bg-surface p-5 shadow-card">
        @csrf
        <label for="name" class="block font-bold">Tambah rombel</label>
        <div class="flex gap-2">
            <input id="name" name="name" value="{{ old('name') }}" required maxlength="20" placeholder="Contoh: 4C"
                class="h-12 min-w-0 flex-1 rounded-control border-2 border-border bg-surface px-4 outline-none focus:border-brand focus:ring-4 focus:ring-brand-100">
            <x-button icon="plus" class="shrink-0">Tambah</x-button>
        </div>
        <p class="text-caption text-text-muted">Format: angka kelas lalu huruf, tanpa strip (4A, bukan 4-A). Sama dengan isi kolom Rombel di template impor.</p>
    </form>

    {{-- Daftar rombel --}}
    <section class="space-y-3">
        <div>
            <h1 class="text-h2 font-extrabold">Daftar rombel ({{ $rombels->count() }})</h1>
            <p class="mt-0.5 text-caption text-text-muted">Pilihan ini dipakai di impor Excel, form akun, pendaftaran, dan form buat kelas.</p>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($rombels as $rombel)
                @php($used = $rombel->students_count > 0 || $rombel->classrooms_count > 0)
                <article class="space-y-2 rounded-card bg-surface p-4 shadow-card">
                    <form method="POST" action="{{ route('admin.rombel.update', $rombel) }}" class="flex gap-2">
                        @csrf
                        @method('PUT')
                        <label for="rombel-{{ $rombel->id }}" class="sr-only">Nama rombel</label>
                        <input id="rombel-{{ $rombel->id }}" name="name" value="{{ $rombel->name }}" required maxlength="20"
                            class="h-12 min-w-0 flex-1 rounded-control border-2 border-border bg-surface px-3 text-h2 font-extrabold outline-none focus:border-brand focus:ring-4 focus:ring-brand-100">
                        <button type="submit" aria-label="Simpan nama rombel {{ $rombel->name }}" class="flex size-12 shrink-0 items-center justify-center rounded-control bg-brand-50 text-brand hover:bg-brand-100">
                            <x-icon name="floppy-disk" class="size-6" />
                        </button>
                    </form>

                    <div class="flex items-center justify-between gap-2">
                        <span class="flex flex-wrap gap-2">
                            <x-chip icon="student">{{ $rombel->students_count }} siswa</x-chip>
                            <x-chip icon="chalkboard-teacher">{{ $rombel->classrooms_count }} kelas</x-chip>
                        </span>
                        @unless ($used)
                            <form method="POST" action="{{ route('admin.rombel.destroy', $rombel) }}"
                                data-confirm-title="Hapus rombel?" data-confirm="{{ 'Rombel '.$rombel->name.' akan dihapus dari daftar.' }}" data-confirm-label="Ya, hapus" data-confirm-tone="danger">
                                @csrf
                                @method('DELETE')
                                <button type="submit" aria-label="Hapus rombel {{ $rombel->name }}" class="flex size-11 items-center justify-center rounded-full text-danger hover:bg-danger-50">
                                    <x-icon name="trash" class="size-5" />
                                </button>
                            </form>
                        @endunless
                    </div>
                </article>
            @empty
                <x-card class="sm:col-span-2">
                    <x-empty-state icon="student" title="Belum ada rombel">Tambahkan dulu sebelum impor siswa.</x-empty-state>
                </x-card>
            @endforelse
        </div>
    </section>

@endsection
