{{-- Rekap nilai satu kelas: tabel siswa × tugas, rata-rata skala 100, unduh Excel. --}}
@extends('layouts.app')

@section('title', 'Rekap Nilai')
@section('back', route('guru.rekap'))
@section('back-label', 'Kembali ke Rekap Nilai')

@section('content')

    <x-card>
        <div class="flex items-center gap-4">
            <x-subject-icon :subject="$classroom->subject" size="lg" />
            <div class="min-w-0 flex-1">
                <h1 class="text-h1 font-extrabold">{{ $classroom->name }}</h1>
                <div class="mt-1 flex flex-wrap gap-2">
                    <x-chip :icon="$classroom->subject->iconName()" tone="brand">{{ $classroom->subject->name }}</x-chip>
                    <x-chip icon="calendar-blank">{{ $classroom->academic_year }}</x-chip>
                </div>
            </div>
        </div>
        <x-button :href="route('kelas.rekap.unduh', $classroom)" icon="microsoft-excel-logo" variant="success" block class="mt-4">Unduh Excel</x-button>
    </x-card>

    @if ($recap->assignments->isEmpty() || $recap->rows->isEmpty())
        <x-card>
            <x-empty-state icon="chart-bar" :title="$recap->rows->isEmpty() ? 'Belum ada siswa' : 'Belum ada tugas'">
                {{ $recap->rows->isEmpty() ? 'Belum ada siswa di kelas ini.' : 'Belum ada tugas di kelas ini.' }}
            </x-empty-state>
        </x-card>
    @else
        <x-card padding="p-0" class="overflow-hidden">
            <p class="flex items-start gap-2 p-4 text-caption text-text-muted">
                <x-icon name="info" class="size-4" />
                Geser tabel ke samping untuk melihat semua tugas. Rata-rata dalam skala 100, hanya dari tugas yang sudah dinilai.
            </p>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-caption">
                    <thead class="bg-background">
                        <tr>
                            <th scope="col" class="sticky left-0 z-10 bg-background px-4 py-3 font-extrabold">Siswa</th>
                            @foreach ($recap->assignments as $assignment)
                                <th scope="col" class="min-w-32 px-3 py-3 align-top font-extrabold">
                                    <a href="{{ route('kelas.tugas.show', [$classroom, $assignment]) }}" class="text-brand hover:underline">{{ $assignment->title }}</a>
                                    <span class="block font-semibold text-text-muted">maks {{ $assignment->max_score }}</span>
                                </th>
                            @endforeach
                            <th scope="col" class="px-3 py-3 font-extrabold">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($recap->rows as $row)
                            <tr>
                                <th scope="row" class="sticky left-0 z-10 max-w-36 bg-surface px-4 py-3 font-bold">{{ $row->student->name }}</th>
                                @foreach ($recap->assignments as $assignment)
                                    @php($cell = $row->cells[$assignment->id])
                                    <td class="px-3 py-3">
                                        @if ($cell->grade)
                                            <span class="text-[17px] font-extrabold">{{ $cell->grade->score }}</span>
                                        @else
                                            <x-status-badge :status="$cell->status" class="px-2! py-0.5! text-[12px]!" />
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-3 py-3">
                                    @if ($row->average !== null)
                                        <span class="rounded-full bg-brand-50 px-3 py-1 font-extrabold text-brand-700">{{ $row->average }}</span>
                                    @else
                                        <span class="text-text-muted">–</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif

@endsection
