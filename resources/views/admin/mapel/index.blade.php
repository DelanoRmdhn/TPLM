{{-- Kelola mata pelajaran (admin): tambah, ganti nama, hapus yang belum dipakai kelas. --}}
@extends('layouts.app')

@section('title', 'Mata Pelajaran')
@section('back', route('admin.beranda'))
@section('back-label', 'Kembali ke Beranda')

@section('content')

    {{-- Tambah mapel --}}
    <form method="POST" action="{{ route('admin.mapel.store') }}" class="space-y-3 rounded-card bg-surface p-5 shadow-card">
        @csrf
        <label for="name" class="block font-bold">Tambah mata pelajaran</label>
        <div class="flex gap-2">
            <input id="name" name="name" value="{{ old('name') }}" required maxlength="100" placeholder="Contoh: Bahasa Sunda"
                class="h-12 min-w-0 flex-1 rounded-control border-2 border-border bg-surface px-4 outline-none focus:border-brand focus:ring-4 focus:ring-brand-100">
            <x-button icon="plus" class="shrink-0">Tambah</x-button>
        </div>
    </form>

    {{-- Daftar mapel --}}
    <section class="space-y-3">
        <div>
            <h1 class="text-h2 font-extrabold">Daftar mata pelajaran ({{ $subjects->count() }})</h1>
            <p class="mt-0.5 text-caption text-text-muted">Pilihan ini muncul di form buat kelas guru. Ganti nama juga mengganti nama mapel di kelas yang sudah ada.</p>
        </div>

        @forelse ($subjects as $subject)
            @php($used = $subject->classrooms_count)
            <article class="space-y-2 rounded-card bg-surface p-4 shadow-card">
                <form method="POST" action="{{ route('admin.mapel.update', $subject) }}" class="flex items-center gap-2">
                    @csrf
                    @method('PUT')
                    <x-subject-icon :subject="$subject" />
                    <label for="mapel-{{ $subject->id }}" class="sr-only">Nama mapel</label>
                    <input id="mapel-{{ $subject->id }}" name="name" value="{{ $subject->name }}" required maxlength="100"
                        class="h-12 min-w-0 flex-1 rounded-control border-2 border-border bg-surface px-3 font-bold outline-none focus:border-brand focus:ring-4 focus:ring-brand-100">
                    <button type="submit" aria-label="Simpan nama {{ $subject->name }}" class="flex size-12 shrink-0 items-center justify-center rounded-control bg-brand-50 text-brand hover:bg-brand-100">
                        <x-icon name="floppy-disk" class="size-6" />
                    </button>
                </form>

                <div class="flex items-center justify-between gap-2">
                    <x-chip icon="chalkboard-teacher" :tone="$used > 0 ? 'brand' : 'neutral'">{{ $used > 0 ? "Dipakai di {$used} kelas" : 'Belum dipakai kelas' }}</x-chip>
                    @if ($used === 0)
                        <form method="POST" action="{{ route('admin.mapel.destroy', $subject) }}"
                            data-confirm-title="Hapus mata pelajaran?" data-confirm="{{ $subject->name.' akan dihapus dari daftar.' }}" data-confirm-label="Ya, hapus" data-confirm-tone="danger">
                            @csrf
                            @method('DELETE')
                            <x-button variant="ghost" size="sm" icon="trash" class="text-danger hover:bg-danger-50">Hapus</x-button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <x-card>
                <x-empty-state icon="books" title="Belum ada mata pelajaran">Tambahkan dulu supaya guru bisa membuat kelas.</x-empty-state>
            </x-card>
        @endforelse
    </section>

@endsection
