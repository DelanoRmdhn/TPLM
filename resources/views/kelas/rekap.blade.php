{{-- Rekap nilai satu kelas — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Rekap · '.$classroom->name)

@section('content')

    <a href="{{ route('guru.rekap') }}" class="inline-flex h-12 items-center text-brand underline">← Rekap Nilai</a>

    <section class="rounded-card border border-border bg-white p-4">
        <p class="text-caption text-text-muted">{{ $classroom->subject->name }} · {{ $classroom->academic_year }}</p>
        <h1 class="text-h1 font-bold">{{ $classroom->name }}</h1>
        <a href="{{ route('kelas.rekap.unduh', $classroom) }}" class="mt-3 flex h-12 items-center justify-center rounded-control bg-brand font-semibold text-white">Unduh Excel</a>
    </section>

    @if ($recap->assignments->isEmpty() || $recap->rows->isEmpty())
        <p class="rounded-card border border-border bg-white p-4 text-text-muted">
            {{ $recap->rows->isEmpty() ? 'Belum ada siswa di kelas ini.' : 'Belum ada tugas di kelas ini.' }}
        </p>
    @else
        <section class="rounded-card border border-border bg-white p-4">
            <p class="text-caption text-text-muted">Angka = nilai. Rata-rata dalam skala 100, hanya dari tugas yang sudah dinilai.</p>

            <div class="mt-2 overflow-x-auto">
                <table class="min-w-full text-left text-caption">
                    <thead>
                        <tr class="border-b border-border">
                            <th scope="col" class="sticky left-0 bg-white py-2 pr-3 font-semibold">Siswa</th>
                            @foreach ($recap->assignments as $assignment)
                                <th scope="col" class="min-w-24 px-2 py-2 font-semibold">
                                    <a href="{{ route('kelas.tugas.show', [$classroom, $assignment]) }}" class="text-brand underline">{{ $assignment->title }}</a>
                                    <span class="block font-normal text-text-muted">maks {{ $assignment->max_score }}</span>
                                </th>
                            @endforeach
                            <th scope="col" class="px-2 py-2 font-semibold">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($recap->rows as $row)
                            <tr>
                                <th scope="row" class="sticky left-0 bg-white py-2 pr-3 font-normal">{{ $row->student->name }}</th>
                                @foreach ($recap->assignments as $assignment)
                                    @php($cell = $row->cells[$assignment->id])
                                    <td class="px-2 py-2">
                                        @if ($cell->grade)
                                            <span class="font-bold">{{ $cell->grade->score }}</span>
                                        @else
                                            <span class="rounded-full px-2 py-0.5 {{ $cell->status->badgeClass() }}">{{ $cell->status->label() }}</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-2 py-2 font-bold">{{ $row->average ?? '–' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

@endsection
