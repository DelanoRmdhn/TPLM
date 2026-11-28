{{-- M06 Tugas versi guru: siapa sudah & belum mengumpulkan, beserta jumlahnya (FR-21, PB-14). --}}
@extends('layouts.app')

@section('title', 'Pengumpulan')
@section('back', route('kelas.show', $classroom))
@section('back-label', 'Kembali ke '.$classroom->name)

@section('content')

    <x-card class="space-y-3">
        <div class="flex items-start gap-4">
            <span class="flex size-14 shrink-0 items-center justify-center rounded-[18px] bg-brand-100 text-brand">
                <x-icon name="clipboard-text" class="size-8" />
            </span>
            <h1 class="min-w-0 flex-1 pt-1 text-h1 font-extrabold">{{ $assignment->title }}</h1>
        </div>

        <div class="flex flex-wrap gap-2">
            <x-chip icon="alarm" :tone="$assignment->isPastDue() ? 'danger' : 'neutral'">{{ $assignment->due_at->translatedFormat('D, j M Y, H.i') }} WIB</x-chip>
            <x-chip icon="star">Nilai maksimal {{ $assignment->max_score }}</x-chip>
        </div>

        @if ($assignment->instructions)
            <p class="whitespace-pre-line">{{ $assignment->instructions }}</p>
        @endif

        @include('tugas._lampiran', ['attachments' => $assignment->attachments])

        @if ($canManage)
            <x-button :href="route('kelas.tugas.edit', [$classroom, $assignment])" variant="secondary" icon="pencil-simple" block>Ubah Tugas</x-button>
        @endif
    </x-card>

    {{-- PB-14: daftar terbagi dua beserta jumlahnya --}}
    <div class="grid grid-cols-2 gap-3">
        <x-stat :value="$sudah->count()" label="Sudah mengumpulkan" icon="paper-plane-tilt" tone="brand" />
        <x-stat :value="$belum->count()" label="Belum mengumpulkan" icon="hourglass-medium" tone="sunny" />
    </div>

    <x-card padding="p-4">
        <h2 class="flex items-center gap-2 text-h2 font-extrabold">
            <x-icon name="paper-plane-tilt" class="size-6 text-brand" />
            Sudah mengumpulkan ({{ $sudah->count() }})
        </h2>
        <ul class="mt-3 space-y-2">
            @forelse ($sudah as $row)
                <li>
                    <a href="{{ route('kelas.tugas.nilai.edit', [$classroom, $assignment, $row->student]) }}"
                        class="flex min-h-16 items-center gap-3 rounded-control bg-background p-3 transition hover:bg-brand-50">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand text-caption font-extrabold text-white">
                            {{ mb_strtoupper(mb_substr($row->student->name, 0, 1)) }}
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-bold">{{ $row->student->name }}</span>
                            @if ($row->submission)
                                <span class="mt-0.5 flex flex-wrap items-center gap-x-2 text-caption text-text-muted">
                                    <span class="inline-flex items-center gap-1"><x-icon name="calendar-check" class="size-4" />{{ $row->submission->submitted_at->translatedFormat('j M, H.i') }}</span>
                                    @if ($row->submission->isLate()) <span class="font-bold text-danger">Terlambat</span> @endif
                                </span>
                            @endif
                        </span>
                        @if ($row->grade)
                            <span class="shrink-0 rounded-full bg-success-50 px-3 py-1 font-extrabold text-success">{{ $row->grade->score }}/{{ $assignment->max_score }}</span>
                        @else
                            <x-status-badge :status="$row->status" class="shrink-0" />
                        @endif
                    </a>
                </li>
            @empty
                <li class="rounded-control bg-background p-3 text-text-muted">Belum ada yang mengumpulkan.</li>
            @endforelse
        </ul>
    </x-card>

    <x-card padding="p-4">
        <h2 class="flex items-center gap-2 text-h2 font-extrabold">
            <x-icon name="hourglass-medium" class="size-6 text-warning" />
            Belum mengumpulkan ({{ $belum->count() }})
        </h2>
        <ul class="mt-3 space-y-2">
            @forelse ($belum as $row)
                <li>
                    <a href="{{ route('kelas.tugas.nilai.edit', [$classroom, $assignment, $row->student]) }}"
                        class="flex min-h-14 items-center gap-3 rounded-control bg-background p-3 transition hover:bg-brand-50">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-border text-caption font-extrabold text-text-muted">
                            {{ mb_strtoupper(mb_substr($row->student->name, 0, 1)) }}
                        </span>
                        <span class="min-w-0 flex-1 truncate font-bold">{{ $row->student->name }}</span>
                        <x-status-badge :status="$row->status" class="shrink-0" />
                    </a>
                </li>
            @empty
                <li class="flex items-center gap-2 rounded-control bg-success-50 p-3 font-semibold text-success">
                    <x-icon name="confetti" variant="fill" class="size-5" />
                    Semua siswa sudah mengumpulkan.
                </li>
            @endforelse
        </ul>
    </x-card>

    @if ($canDelete)
        <form method="POST" action="{{ route('kelas.tugas.destroy', [$classroom, $assignment]) }}"
            data-confirm-title="Hapus tugas ini?" data-confirm="{{ $assignment->title.' akan dihapus dan siswa tidak bisa membukanya lagi.' }}" data-confirm-label="Ya, hapus" data-confirm-tone="danger">
            @csrf
            @method('DELETE')
            <x-button variant="ghost" icon="trash" block class="text-danger hover:bg-danger-50">Hapus Tugas</x-button>
        </form>
    @else
        <p class="flex items-center justify-center gap-1.5 text-center text-caption text-text-muted">
            <x-icon name="lock-key" class="size-4" />
            Tugas ini sudah dikumpulkan siswa, jadi tidak bisa dihapus.
        </p>
    @endif

@endsection
