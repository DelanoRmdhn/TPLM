{{-- Menu Penilaian guru — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Penilaian')

@section('content')

    <a href="{{ route('guru.beranda') }}" class="inline-flex h-12 items-center text-brand underline">← Beranda</a>

    <section class="rounded-card border border-border bg-white p-4">
        <h1 class="text-h1 font-bold">Perlu Dinilai</h1>
        <p class="mt-1 text-text-muted">
            @if ($total > 0)
                {{ $total }} tugas siswa menunggu nilai. Yang paling lama dikirim ada di atas.
            @else
                Semua pengumpulan sudah dinilai.
            @endif
        </p>
    </section>

    @foreach ($classes as $kelas)
        <section class="rounded-card border border-border bg-white p-4">
            <h2 class="font-bold">{{ $kelas->classroom->name }}</h2>

            @foreach ($kelas->assignments as $tugas)
                <div class="mt-3">
                    <a href="{{ route('kelas.tugas.show', [$kelas->classroom, $tugas->assignment]) }}" class="font-semibold text-brand underline">
                        {{ $tugas->assignment->title }}
                    </a>
                    <span class="text-caption text-text-muted">· {{ $tugas->submissions->count() }} belum dinilai</span>

                    <ul class="mt-1 divide-y divide-border">
                        @foreach ($tugas->submissions as $submission)
                            <li>
                                <a href="{{ route('kelas.tugas.nilai.edit', [$kelas->classroom, $tugas->assignment, $submission->student]) }}"
                                    class="flex min-h-12 items-center justify-between gap-2 py-2">
                                    <span>{{ $submission->student->name }}</span>
                                    <span class="text-caption text-text-muted">
                                        {{ $submission->submitted_at->diffForHumans() }}
                                        @if ($submission->isLate()) · <span class="font-semibold text-danger">Terlambat</span> @endif
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </section>
    @endforeach

@endsection
