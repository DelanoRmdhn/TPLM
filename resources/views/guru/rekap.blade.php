{{-- Menu Rekap guru: pilih kelas — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Rekap Nilai')

@section('content')

    <a href="{{ route('guru.beranda') }}" class="inline-flex h-12 items-center text-brand underline">← Beranda</a>

    <section class="rounded-card border border-border bg-white p-4">
        <h1 class="text-h1 font-bold">Rekap Nilai</h1>
        <p class="mt-1 text-text-muted">Pilih kelas untuk melihat nilai semua siswa dan mengunduhnya sebagai Excel.</p>
    </section>

    @foreach (['Kelas Saya' => $classrooms, 'Kelas diarsipkan' => $archivedClassrooms] as $judul => $daftar)
        @if ($daftar->isNotEmpty() || $loop->first)
            <section class="rounded-card border border-border bg-white p-4">
                <h2 class="font-bold">{{ $judul }}</h2>
                <ul class="mt-2 divide-y divide-border">
                    @forelse ($daftar as $kelas)
                        <li>
                            <a href="{{ route('kelas.rekap', $kelas) }}" class="flex min-h-12 items-center justify-between gap-2 py-2">
                                <span class="font-semibold">{{ $kelas->name }}</span>
                                <span class="text-caption text-text-muted">{{ $kelas->students_count }} siswa · {{ $kelas->assignments_count }} tugas · {{ $kelas->academic_year }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="py-3 text-text-muted">Belum ada kelas.</li>
                    @endforelse
                </ul>
            </section>
        @endif
    @endforeach

@endsection
