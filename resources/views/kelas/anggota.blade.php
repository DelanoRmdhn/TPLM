{{-- S13 Anggota kelas + pop-up tambah rombel — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Anggota · '.$classroom->name)

@section('content')

    <a href="{{ route(auth()->user()->homeRoute()) }}" class="inline-flex h-12 items-center text-brand underline">← Kembali ke beranda</a>

    <section class="rounded-card border border-border bg-white p-4">
        <p class="text-caption text-text-muted">{{ $classroom->subject }} · {{ $classroom->academic_year }}</p>
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
                        <span class="text-caption text-text-muted">{{ $student->username }} · {{ $student->rombel?->name }}</span>
                    @endif
                </li>
            @empty
                <li class="py-3 text-text-muted">Belum ada siswa.</li>
            @endforelse
        </ul>
    </section>

@endsection
