{{-- M06 Pengumpulan & penilaian (guru) — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', $assignment->title)

@section('content')

    <a href="{{ route('kelas.show', $classroom) }}" class="inline-flex h-12 items-center text-brand underline">← {{ $classroom->name }}</a>

    <section class="space-y-2 rounded-card border border-border bg-white p-4">
        <h1 class="text-h1 font-bold">{{ $assignment->title }}</h1>
        <p class="text-text-muted">
            Tenggat {{ $assignment->due_at->translatedFormat('l, j F Y, H.i') }} WIB · Nilai maks {{ $assignment->max_score }}
        </p>
        @if ($assignment->instructions)
            <p class="whitespace-pre-line">{{ $assignment->instructions }}</p>
        @endif
        @include('tugas._lampiran', ['attachments' => $assignment->attachments])
        <a href="{{ route('kelas.tugas.edit', [$classroom, $assignment]) }}" class="flex h-12 items-center justify-center rounded-control border border-brand font-semibold text-brand">Ubah Tugas</a>
    </section>

    {{-- PB-14: daftar terbagi dua beserta jumlahnya --}}
    <section class="grid grid-cols-2 gap-3">
        <div class="rounded-card border border-blue-200 bg-blue-50 p-4">
            <p class="text-caption text-blue-800">Sudah mengumpulkan</p>
            <p class="text-h1 font-bold text-blue-800">{{ $sudah->count() }}</p>
        </div>
        <div class="rounded-card border border-amber-200 bg-amber-50 p-4">
            <p class="text-caption text-amber-800">Belum mengumpulkan</p>
            <p class="text-h1 font-bold text-amber-800">{{ $belum->count() }}</p>
        </div>
    </section>

    <section class="rounded-card border border-border bg-white p-4">
        <h2 class="font-bold">Sudah mengumpulkan ({{ $sudah->count() }})</h2>
        <ul class="mt-2 divide-y divide-border">
            @forelse ($sudah as $row)
                <li>
                    <a href="{{ route('kelas.tugas.nilai.edit', [$classroom, $assignment, $row->student]) }}" class="flex min-h-12 items-center justify-between gap-2 py-3">
                        <span>
                            <span class="block font-semibold">{{ $row->student->name }}</span>
                            <span class="text-caption text-text-muted">
                                @if ($row->submission)
                                    {{ $row->submission->submitted_at->translatedFormat('j M, H.i') }}
                                    @if ($row->submission->isLate()) · <span class="font-semibold text-danger">Terlambat</span> @endif
                                @endif
                            </span>
                        </span>
                        <span class="shrink-0 rounded-full px-3 py-1 text-caption {{ $row->status->badgeClass() }}">
                            {{ $row->grade ? $row->grade->score.'/'.$assignment->max_score : $row->status->label() }}
                        </span>
                    </a>
                </li>
            @empty
                <li class="py-3 text-text-muted">Belum ada yang mengumpulkan.</li>
            @endforelse
        </ul>
    </section>

    <section class="rounded-card border border-border bg-white p-4">
        <h2 class="font-bold">Belum mengumpulkan ({{ $belum->count() }})</h2>
        <ul class="mt-2 divide-y divide-border">
            @forelse ($belum as $row)
                <li>
                    <a href="{{ route('kelas.tugas.nilai.edit', [$classroom, $assignment, $row->student]) }}" class="flex min-h-12 items-center justify-between gap-2 py-3">
                        <span class="font-semibold">{{ $row->student->name }}</span>
                        <span class="shrink-0 rounded-full px-3 py-1 text-caption {{ $row->status->badgeClass() }}">{{ $row->status->label() }}</span>
                    </a>
                </li>
            @empty
                <li class="py-3 text-text-muted">Semua siswa sudah mengumpulkan. 🎉</li>
            @endforelse
        </ul>
    </section>

    @if ($canDelete)
        <form method="POST" action="{{ route('kelas.tugas.destroy', [$classroom, $assignment]) }}"
            onsubmit="return confirm(@js('Hapus tugas '.$assignment->title.'? Siswa tidak bisa membukanya lagi.'))">
            @csrf
            @method('DELETE')
            <button type="submit" class="h-12 w-full rounded-control border border-red-300 text-danger">Hapus Tugas</button>
        </form>
    @else
        <p class="text-center text-caption text-text-muted">Tugas ini sudah dikumpulkan siswa, jadi tidak bisa dihapus.</p>
    @endif

@endsection
