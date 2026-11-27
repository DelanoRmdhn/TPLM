{{-- S13 Anggota kelas + pop-up tambah rombel — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Anggota · '.$classroom->name)

@section('content')

    <a href="{{ route('kelas.show', $classroom) }}" class="inline-flex h-12 items-center text-brand underline">← Kembali ke kelas</a>

    <section class="rounded-card border border-border bg-white p-4">
        <p class="text-caption text-text-muted">{{ $classroom->subject->name }} · {{ $classroom->academic_year }}</p>
        <h1 class="text-h1 font-bold">{{ $classroom->name }}</h1>
    </section>

    @if ($canManage)
        <form method="POST" action="{{ route('kelas.anggota.rombel', $classroom) }}"
            class="space-y-3 rounded-card border border-border bg-white p-4">
            @csrf
            <label for="rombel" class="block font-semibold">Tambah satu rombel</label>
            <select id="rombel" name="rombel" required class="h-12 w-full rounded-control border border-border px-3">
                <option value="">Pilih rombel</option>
                @foreach ($rombels as $rombel)
                    <option value="{{ $rombel->id }}">{{ $rombel->name }} · {{ $rombel->students_count }} siswa</option>
                @endforeach
            </select>
            <p class="text-caption text-text-muted">Semua siswa rombel itu masuk sekaligus. Yang sudah ada tidak dobel.</p>
            <button type="submit" class="h-12 w-full rounded-control bg-brand font-semibold text-white">Tambahkan</button>
        </form>

        <form method="POST" action="{{ route('kelas.anggota.store', $classroom) }}"
            class="space-y-3 rounded-card border border-border bg-white p-4">
            @csrf
            <label for="nisn" class="block font-semibold">Tambah satu siswa</label>
            <input id="nisn" name="nisn" required inputmode="numeric" maxlength="50" value="{{ old('nisn') }}" placeholder="NISN siswa"
                class="h-12 w-full rounded-control border border-border px-3">
            <button type="submit" class="h-12 w-full rounded-control border border-brand font-semibold text-brand">Tambahkan Siswa</button>
        </form>
    @endif

    <section class="rounded-card border border-border bg-white p-4">
        <h2 class="font-bold">Guru</h2>
        <p class="mt-2">{{ $classroom->teacher->name }}</p>
    </section>

    <section class="rounded-card border border-border bg-white p-4">
        <h2 class="font-bold">Siswa ({{ $students->count() }})</h2>

        <ul class="mt-2 divide-y divide-border">
            @forelse ($students as $student)
                <li class="flex items-center justify-between gap-2 py-3">
                    <span>{{ $student->name }}</span>
                    {{-- NISN hanya untuk guru (NFR-10) --}}
                    @if ($canManage)
                        <span class="flex items-center gap-2">
                            <span class="text-caption text-text-muted">{{ $student->username }} · {{ $student->rombel?->name }}</span>
                            <form method="POST" action="{{ route('kelas.anggota.destroy', [$classroom, $student]) }}"
                                onsubmit="return confirm(@js('Keluarkan '.$student->name.' dari kelas?'))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" aria-label="Keluarkan {{ $student->name }}" class="h-12 rounded-control border border-red-300 px-3 text-caption font-semibold text-danger">Keluarkan</button>
                            </form>
                        </span>
                    @endif
                </li>
            @empty
                <li class="py-3 text-text-muted">Belum ada siswa.</li>
            @endforelse
        </ul>
    </section>

@endsection
