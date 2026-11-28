{{-- M07 Nilai Saya (FR-25, PB-16): hanya nilai milik sendiri, per kelas. --}}
@extends('layouts.app')

@section('title', 'Nilai Saya')

@section('content')

    @php
        // Rata-rata skala 100, karena nilai maksimal tiap tugas bisa berbeda
        $scaled = fn ($rows) => round($rows->avg(fn ($row) => $row->grade->score / $row->assignment->max_score * 100));
        $allRows = $classes->flatten(1);
    @endphp

    @if ($allRows->isEmpty())
        <x-card>
            <x-empty-state icon="star" title="Belum ada nilai">Belum ada tugas yang dinilai. Nilai dari gurumu akan muncul di sini.</x-empty-state>
        </x-card>
    @else
        {{-- Rata-rata keseluruhan --}}
        <section class="relative overflow-hidden rounded-[26px] bg-brand p-5 text-white shadow-card">
            <span class="pointer-events-none absolute -right-8 -bottom-10 size-36 rounded-full bg-white/10" aria-hidden="true"></span>
            <div class="relative flex items-center gap-4">
                <span class="flex size-16 shrink-0 items-center justify-center rounded-full bg-sunny text-[#7a4b00] motion-safe:animate-pop">
                    <x-icon name="trophy" variant="fill" class="size-9" />
                </span>
                <div>
                    <p class="font-semibold opacity-90">Rata-rata nilaimu</p>
                    <p class="text-[40px] leading-none font-extrabold">{{ $scaled($allRows) }}</p>
                    <p class="mt-1 text-caption opacity-90">dari {{ $allRows->count() }} tugas yang sudah dinilai</p>
                </div>
            </div>
        </section>

        {{-- Kotak rata-rata per kelas, berwarna mapel --}}
        <div class="grid grid-cols-2 gap-3">
            @foreach ($classes as $className => $rows)
                @php($subject = $rows->first()->assignment->classroom->subject)
                <a href="#kelas-{{ $loop->index }}" class="relative overflow-hidden rounded-[22px] p-4 shadow-card active:scale-[0.98] {{ $subject->solidClasses() }}">
                    <span class="absolute -top-3 -right-3 flex size-14 items-center justify-center rounded-full bg-white/25" aria-hidden="true">
                        <x-icon :name="$subject->iconName()" variant="fill" class="size-6" />
                    </span>
                    <p class="truncate pr-10 text-caption font-bold opacity-90">{{ $className }}</p>
                    <p class="mt-2 text-[34px] leading-none font-extrabold">{{ $scaled($rows) }}</p>
                    <p class="mt-1 text-[12px] font-semibold opacity-90">{{ $rows->count() }} tugas dinilai</p>
                </a>
            @endforeach
        </div>

        {{-- PB-16: hanya nilai milik sendiri, per kelas --}}
        @foreach ($classes as $className => $rows)
            <section id="kelas-{{ $loop->index }}" class="scroll-mt-20 space-y-2 pt-2">
                <x-section-title>{{ $className }}</x-section-title>

                @foreach ($rows as $row)
                    <a href="{{ route('kelas.tugas.show', [$row->assignment->classroom_id, $row->assignment]) }}"
                        class="block rounded-[22px] bg-surface p-4 shadow-card transition active:scale-[0.99]">
                        <span class="flex items-center gap-3">
                            <span class="min-w-0 flex-1">
                                <span class="block font-extrabold">{{ $row->assignment->title }}</span>
                                <span class="mt-0.5 flex items-center gap-1 text-caption text-text-muted">
                                    <x-icon name="calendar-check" class="size-4" />
                                    Dinilai {{ $row->grade->updated_at->translatedFormat('j M Y') }}
                                </span>
                            </span>
                            <span class="flex size-14 shrink-0 flex-col items-center justify-center rounded-[18px] bg-success-50 text-success">
                                <span class="text-[20px] leading-none font-extrabold">{{ $row->grade->score }}</span>
                                <span class="text-[11px] font-bold">/{{ $row->assignment->max_score }}</span>
                            </span>
                        </span>
                        @if ($row->grade->feedback)
                            <span class="mt-3 flex gap-2 rounded-[16px] rounded-tl-[4px] bg-background p-3 text-caption">
                                <x-icon name="chat-circle-text" variant="fill" class="size-5 text-brand" />
                                <span>{{ $row->grade->feedback }}</span>
                            </span>
                        @endif
                    </a>
                @endforeach
            </section>
        @endforeach
    @endif

@endsection
