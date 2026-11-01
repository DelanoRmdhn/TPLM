{{-- M06 Beri nilai satu siswa — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Nilai · '.$student->name)

@section('content')

    <a href="{{ route('kelas.tugas.show', [$classroom, $assignment]) }}" class="inline-flex h-12 items-center text-brand underline">← {{ $assignment->title }}</a>

    <section class="space-y-2 rounded-card border border-border bg-white p-4">
        <span class="inline-block rounded-full px-3 py-1 text-caption font-semibold {{ $status->badgeClass() }}">{{ $status->label() }}</span>
        <h1 class="text-h1 font-bold">{{ $student->name }}</h1>

        @if ($submission?->isSubmitted())
            <p class="text-text-muted">
                Diserahkan {{ $submission->submitted_at->translatedFormat('l, j F Y, H.i') }} WIB
                @if ($submission->isLate()) · <span class="font-semibold text-danger">Terlambat</span> @endif
            </p>

            @if ($submission->body)
                <p class="whitespace-pre-line rounded-control bg-slate-50 p-3">{{ $submission->body }}</p>
            @endif

            <div class="space-y-3">
                @foreach ($submission->files as $file)
                    @if ($file->isImage())
                        <a href="{{ route('pengumpulan.file', $file) }}" target="_blank">
                            <img src="{{ route('pengumpulan.file', $file) }}" alt="Foto tugas {{ $student->name }}" class="w-full rounded-control border border-border" loading="lazy">
                        </a>
                    @else
                        <a href="{{ route('pengumpulan.file', $file) }}" target="_blank" class="flex min-h-12 items-center rounded-control border border-border px-3">📄 {{ $file->original_name }}</a>
                    @endif
                @endforeach
            </div>
        @else
            <p class="text-text-muted">Siswa ini belum mengumpulkan. Kamu tetap bisa memberi nilai.</p>
        @endif
    </section>

    {{-- PB-15: nilai 0 sampai nilai maksimal + umpan balik teks --}}
    <form method="POST" action="{{ route('kelas.tugas.nilai.update', [$classroom, $assignment, $student]) }}"
        class="space-y-3 rounded-card border border-border bg-white p-4">
        @csrf
        @method('PUT')

        <div>
            <label for="score" class="mb-1 block font-semibold">Nilai (0–{{ $assignment->max_score }})</label>
            <input id="score" name="score" type="number" min="0" max="{{ $assignment->max_score }}" required inputmode="numeric"
                value="{{ old('score', $grade?->score) }}" class="h-12 w-full rounded-control border border-border px-3 text-h1 font-bold">
        </div>

        <div>
            <label for="feedback" class="mb-1 block font-semibold">Umpan balik (boleh kosong)</label>
            <textarea id="feedback" name="feedback" rows="3" maxlength="2000" placeholder="Contoh: Bagus! Perhatikan lagi nomor 3."
                class="w-full rounded-control border border-border p-3">{{ old('feedback', $grade?->feedback) }}</textarea>
        </div>

        <button type="submit" class="h-12 w-full rounded-control bg-brand font-semibold text-white">
            {{ $grade ? 'Perbarui Nilai' : 'Simpan Nilai' }}
        </button>
    </form>

@endsection
