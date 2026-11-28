{{-- M06 Beri nilai satu siswa: lihat pengumpulan, isi nilai 0..maks + umpan balik (FR-23, PB-15). --}}
@extends('layouts.app')

@section('title', 'Beri Nilai')
@section('back', route('kelas.tugas.show', [$classroom, $assignment]))
@section('back-label', 'Kembali ke '.$assignment->title)

@section('content')

    {{-- Desktop: pengumpulan siswa di kiri, form nilai di kanan --}}
    <div class="grid gap-4 lg:grid-cols-12 lg:items-start lg:gap-6">
    <div class="lg:col-span-7">

    <x-card class="space-y-3">
        <div class="flex items-center gap-3">
            <span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-brand text-h2 font-extrabold text-white">
                {{ mb_strtoupper(mb_substr($student->name, 0, 1)) }}
            </span>
            <div class="min-w-0 flex-1">
                <h1 class="truncate text-h1 font-extrabold">{{ $student->name }}</h1>
                <p class="truncate text-caption text-text-muted">{{ $assignment->title }}</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <x-status-badge :status="$status" />
            @if ($submission?->isSubmitted())
                <x-chip icon="calendar-check">{{ $submission->submitted_at->translatedFormat('j M Y, H.i') }} WIB</x-chip>
                @if ($submission->isLate())
                    <x-chip icon="warning-circle" tone="danger">Terlambat</x-chip>
                @endif
            @endif
        </div>

        @if ($submission?->isSubmitted())
            @if ($submission->body)
                <p class="whitespace-pre-line rounded-control bg-background p-4">{{ $submission->body }}</p>
            @endif

            <div class="space-y-2">
                @foreach ($submission->files as $file)
                    @if ($file->isImage())
                        <a href="{{ route('pengumpulan.file', $file) }}" target="_blank" class="block overflow-hidden rounded-control bg-background">
                            <img src="{{ route('pengumpulan.file', $file) }}" alt="Foto tugas {{ $student->name }}" class="w-full" loading="lazy">
                        </a>
                    @else
                        <a href="{{ route('pengumpulan.file', $file) }}" target="_blank"
                            class="flex min-h-14 items-center gap-3 rounded-control border-2 border-border p-2 pr-3 hover:border-brand-200">
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-[12px] bg-brand-50 text-brand">
                                <x-icon :name="$file->isPdf() ? 'file-pdf' : 'file-doc'" class="size-6" />
                            </span>
                            <span class="min-w-0 flex-1 truncate font-bold">{{ $file->original_name }}</span>
                            <x-icon name="arrow-square-out" class="size-5 text-text-muted" />
                        </a>
                    @endif
                @endforeach
            </div>
        @else
            <p class="flex items-start gap-2 rounded-control bg-background p-3 text-text-muted">
                <x-icon name="info" class="mt-0.5 size-5" />
                Siswa ini belum mengumpulkan. Kamu tetap bisa memberi nilai.
            </p>
        @endif
    </x-card>
    </div>

    <div class="lg:sticky lg:top-6 lg:col-span-5">


    {{-- PB-15: nilai 0 sampai nilai maksimal + umpan balik teks --}}
    @if (! $canManage)
        <x-card>
            <p class="flex items-start gap-2 text-text-muted">
                <x-icon name="archive" class="mt-0.5 size-5" />
                <span>Kelas ini diarsipkan. Nilai: <strong class="text-text">{{ $grade ? $grade->score.' / '.$assignment->max_score : 'belum dinilai' }}</strong></span>
            </p>
        </x-card>
    @else
        <form method="POST" action="{{ route('kelas.tugas.nilai.update', [$classroom, $assignment, $student]) }}" class="space-y-4 rounded-card bg-surface p-5 shadow-card">
            @csrf
            @method('PUT')

            <h2 class="flex items-center gap-2 text-h2 font-extrabold">
                <x-icon name="star" class="size-6 text-brand" />
                Nilai
            </h2>

            <div>
                <label for="score" class="mb-1.5 block font-bold">Nilai (0 sampai {{ $assignment->max_score }})</label>
                <div class="flex items-center gap-3">
                    <input id="score" name="score" type="number" min="0" max="{{ $assignment->max_score }}" required inputmode="numeric"
                        value="{{ old('score', $grade?->score) }}" placeholder="0"
                        class="h-16 w-32 rounded-control border-2 border-border bg-surface px-4 text-center text-display font-extrabold outline-none focus:border-brand focus:ring-4 focus:ring-brand-100">
                    <span class="text-h2 font-bold text-text-muted">/ {{ $assignment->max_score }}</span>
                </div>
            </div>

            <x-textarea name="feedback" label="Pesan untuk siswa" :value="$grade?->feedback" rows="3" maxlength="2000" optional
                placeholder="Contoh: Bagus! Perhatikan lagi nomor 3." />

            <x-button icon="check-circle" size="lg" block>{{ $grade ? 'Perbarui Nilai' : 'Simpan Nilai' }}</x-button>
        </form>
    @endif

    </div>
    </div>

@endsection
