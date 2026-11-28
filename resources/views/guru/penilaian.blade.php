{{-- Menu Penilaian guru: pengumpulan yang belum dinilai dari semua kelas, yang paling lama dikirim di atas. --}}
@extends('layouts.app')

@section('title', 'Penilaian')

@section('content')

    @if ($total > 0)
        <section class="flex items-center gap-4 rounded-card bg-sunny-50 p-5 ring-2 ring-sunny/60">
            <span class="flex size-14 shrink-0 items-center justify-center rounded-full bg-sunny text-[#7a4b00]">
                <x-icon name="list-checks" variant="fill" class="size-8" />
            </span>
            <div>
                <p class="text-h1 font-extrabold">Perlu Dinilai</p>
                <p class="text-[#8a5a00]">{{ $total }} tugas siswa menunggu nilai. Yang paling lama dikirim ada di atas.</p>
            </div>
        </section>
    @else
        <x-card>
            <x-empty-state icon="confetti" title="Semua beres!">Semua pengumpulan sudah dinilai.</x-empty-state>
        </x-card>
    @endif

    @foreach ($classes as $kelas)
        <x-card padding="p-4">
            <div class="flex items-center gap-3">
                <x-subject-icon :subject="$kelas->classroom->subject" />
                <h2 class="min-w-0 flex-1 text-h2 font-extrabold">{{ $kelas->classroom->name }}</h2>
            </div>

            @foreach ($kelas->assignments as $tugas)
                <div class="mt-4">
                    <a href="{{ route('kelas.tugas.show', [$kelas->classroom, $tugas->assignment]) }}" class="flex items-center gap-2 font-extrabold text-brand">
                        <x-icon name="clipboard-text" class="size-5" />
                        <span class="min-w-0 flex-1">{{ $tugas->assignment->title }}</span>
                        <x-chip tone="sunny">{{ $tugas->submissions->count() }} belum dinilai</x-chip>
                    </a>

                    <ul class="mt-2 space-y-2">
                        @foreach ($tugas->submissions as $submission)
                            <li>
                                <a href="{{ route('kelas.tugas.nilai.edit', [$kelas->classroom, $tugas->assignment, $submission->student]) }}"
                                    class="flex min-h-14 items-center gap-3 rounded-control bg-background p-3 transition hover:bg-brand-50">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand text-caption font-extrabold text-white">
                                        {{ mb_strtoupper(mb_substr($submission->student->name, 0, 1)) }}
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate font-bold">{{ $submission->student->name }}</span>
                                        <span class="mt-0.5 flex flex-wrap items-center gap-x-2 text-caption text-text-muted">
                                            <span class="inline-flex items-center gap-1"><x-icon name="clock" class="size-4" />{{ $submission->submitted_at->diffForHumans() }}</span>
                                            @if ($submission->isLate()) <span class="font-bold text-danger">Terlambat</span> @endif
                                        </span>
                                    </span>
                                    <x-icon name="caret-right" variant="bold" class="size-5 text-text-muted" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </x-card>
    @endforeach

@endsection
