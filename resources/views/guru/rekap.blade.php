{{-- Menu Rekap guru: pilih kelas yang mau direkap (kelas berjalan & arsip). --}}
@extends('layouts.app')

@section('title', 'Rekap Nilai')

@section('content')

    <p class="flex items-start gap-2 text-text-muted">
        <x-icon name="info" class="mt-0.5 size-5" />
        Pilih kelas untuk melihat nilai semua siswa dan mengunduhnya sebagai Excel.
    </p>

    @foreach (['Kelas Saya' => $classrooms, 'Kelas diarsipkan' => $archivedClassrooms] as $judul => $daftar)
        @if ($daftar->isNotEmpty() || $loop->first)
            <section class="space-y-3 pt-1">
                <x-section-title :icon="$loop->first ? 'chalkboard-teacher' : 'archive'">{{ $judul }}</x-section-title>
                <div class="grid gap-3 lg:grid-cols-2">
                @forelse ($daftar as $kelas)
                    <a href="{{ route('kelas.rekap', $kelas) }}" class="flex items-center gap-4 rounded-card bg-surface p-4 shadow-card transition active:scale-[0.99]">
                        <x-subject-icon :subject="$kelas->subject" />
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-extrabold">{{ $kelas->name }}</span>
                            <span class="mt-0.5 flex flex-wrap items-center gap-x-3 text-caption text-text-muted">
                                <span class="inline-flex items-center gap-1"><x-icon name="users-three" class="size-4" />{{ $kelas->students_count }} siswa</span>
                                <span class="inline-flex items-center gap-1"><x-icon name="clipboard-text" class="size-4" />{{ $kelas->assignments_count }} tugas</span>
                                <span class="inline-flex items-center gap-1"><x-icon name="calendar-blank" class="size-4" />{{ $kelas->academic_year }}</span>
                            </span>
                        </span>
                        <x-icon name="caret-right" variant="bold" class="size-5 text-text-muted" />
                    </a>
                @empty
                    <x-card>
                        <x-empty-state icon="chalkboard-teacher" title="Belum ada kelas">Buat kelas dulu dari Beranda.</x-empty-state>
                    </x-card>
                @endforelse
                </div>
            </section>
        @endif
    @endforeach

@endsection
