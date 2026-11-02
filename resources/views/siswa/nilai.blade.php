
{{-- M07 Nilai Saya --}}
@extends('layouts.sementara')

@section('title', 'Nilai Saya')

@section('content')
@section('standalone', true)
@php
    $allRows = collect($classes)->flatten(1);

    $totalTasks = $allRows->count();

    $averageScore = $totalTasks > 0
        ? $allRows->avg(fn ($row) => $row->grade->score)
        : 0;

    $subjects = collect($classes)->keys();
@endphp

<div class="mx-auto min-h-screen w-full max-w-[390px] bg-slate-50 pb-24">

    {{-- HEADER --}}
    <header class="border-b border-slate-200 bg-white px-4 py-5">
        <h1 class="text-2xl leading-tight font-bold text-slate-900">
            Nilai Saya
        </h1>

        <p class="mt-1 text-sm text-slate-600">
            {{ auth()->user()->name }}
            ·
            {{ auth()->user()->rombel
                ? 'Kelas '.auth()->user()->rombel->name
                : 'Siswa' }}
        </p>
    </header>

    <main class="space-y-4 px-4 pt-5">

        {{-- RINGKASAN NILAI --}}
        <section class="rounded-2xl bg-blue-600 p-5 text-white">

            <p class="text-base font-medium">
                Rata-rata nilaimu
            </p>

            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-4xl leading-none font-bold">
                    {{ number_format($averageScore, 0) }}
                </span>

                <span class="text-base">
                    dari 100
                </span>
            </div>

            <p class="mt-2 text-sm text-blue-50">
                {{ $totalTasks }} tugas sudah dinilai
            </p>

        </section>

        {{-- FILTER MATA PELAJARAN --}}
        <section class="flex gap-2 overflow-x-auto pb-0.5"
            style="scrollbar-width: none;">

            <button
                type="button"
                data-filter="all"
                class="filter-button shrink-0 rounded-full bg-blue-600 px-[18px] py-3 text-sm font-semibold text-white">
                Semua
            </button>

            @foreach ($subjects as $subject)
                <button
                    type="button"
                    data-filter="{{ $loop->index }}"
                    class="filter-button shrink-0 rounded-full border border-slate-300 bg-white px-[18px] py-3 text-sm font-semibold text-slate-900">
                    {{ $subject }}
                </button>
            @endforeach

        </section>

        {{-- DAFTAR NILAI --}}
        <section class="space-y-4">

            @forelse ($classes as $className => $rows)

                @foreach ($rows as $row)

                    <a
                        href="{{ route('kelas.tugas.show', [
                            $row->assignment->classroom_id,
                            $row->assignment
                        ]) }}"
                        data-subject="{{ $loop->parent->index }}"
                        class="grade-card block rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-blue-300">

                        {{-- MATA PELAJARAN DAN NILAI --}}
                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0 flex-1">

                                <p class="text-sm font-semibold text-blue-600">
                                    {{ $className }}
                                </p>

                                <h2 class="mt-1 text-base leading-snug font-semibold text-slate-900">
                                    {{ $row->assignment->title }}
                                </h2>

                            </div>

                            <div class="shrink-0 rounded-xl bg-green-100 px-3 py-2 text-green-700">

                                <div class="flex items-baseline gap-0.5">
                                    <span class="text-xl leading-none font-bold">
                                        {{ $row->grade->score }}
                                    </span>

                                    <span class="text-sm font-medium">
                                        /{{ $row->assignment->max_score }}
                                    </span>
                                </div>

                            </div>

                        </div>

                        {{-- FEEDBACK GURU --}}
                        @if ($row->grade->feedback)

                            <div class="mt-3 rounded-xl bg-slate-100 p-3">

                                <div class="flex items-start gap-2.5">

                                    <svg
                                        viewBox="0 0 24 24"
                                        class="mt-0.5 size-5 shrink-0 text-slate-600"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true">

                                        <path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8A8.5 8.5 0 0 1 12.5 3h.5a8.5 8.5 0 0 1 8 8v.5Z"/>

                                    </svg>

                                    <div class="min-w-0">

                                        <p class="text-sm font-semibold text-slate-900">
                                            {{ $row->assignment->classroom->teacher->name ?? 'Guru' }}
                                        </p>

                                        <p class="mt-1 text-sm leading-6 text-slate-700">
                                            {{ $row->grade->feedback }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                        @endif

                        {{-- TANGGAL PENILAIAN --}}
                        <p class="mt-3 text-sm text-slate-600">
                            Dinilai
                            {{ $row->grade->updated_at->translatedFormat('j M Y') }}
                        </p>

                    </a>

                @endforeach

            @empty

                <div class="rounded-2xl border border-slate-200 bg-white p-4 text-sm text-slate-600">
                    Belum ada tugas yang dinilai.
                </div>

            @endforelse

        </section>

    </main>

</div>

{{-- BOTTOM NAVIGATION --}}
<nav
    aria-label="Navigasi utama"
    class="fixed inset-x-0 bottom-0 z-50 border-t border-slate-200 bg-white">

    <div class="mx-auto grid h-[69px] w-full max-w-[390px] grid-cols-5 px-1">

        {{-- BERANDA --}}
        <a
            href="{{ route('siswa.beranda') }}"
            class="flex flex-col items-center justify-center gap-0.5 text-slate-600">

            <svg viewBox="0 0 24 24" class="size-6"
                fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round">

                <path d="m3 10 9-7 9 7"/>
                <path d="M5 9v11h14V9"/>
                <path d="M9 20v-7h6v7"/>

            </svg>

            <span class="text-sm font-medium">Beranda</span>
        </a>

        {{-- TUGAS --}}
        <a
            href="{{ route('siswa.tugas') }}"
            class="flex flex-col items-center justify-center gap-0.5 text-slate-600">

            <svg viewBox="0 0 24 24" class="size-6"
                fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round">

                <rect x="5" y="4" width="14" height="17" rx="2"/>
                <path d="M9 4.5V3h6v1.5"/>
                <path d="m9 12 2 2 4-4"/>

            </svg>

            <span class="text-sm font-medium">Tugas</span>
        </a>

        {{-- NILAI AKTIF --}}
        <a
            href="{{ route('siswa.nilai') }}"
            aria-current="page"
            class="flex flex-col items-center justify-center gap-0.5 text-blue-600">

            <span class="flex h-8 w-14 items-center justify-center rounded-full bg-blue-100">

                <svg viewBox="0 0 24 24" class="size-6"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">

                    <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/>

                </svg>

            </span>

            <span class="text-sm font-semibold">Nilai</span>
        </a>

        {{-- NOTIFIKASI --}}
        <a
            href="#"
            class="flex flex-col items-center justify-center gap-0.5 text-slate-600">

            <svg viewBox="0 0 24 24" class="size-6"
                fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round">

                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                <path d="M10 21h4"/>

            </svg>

            <span class="text-sm font-medium">Notifikasi</span>
        </a>

        {{-- AKUN --}}
        <a
            href="{{ route('akun') }}"
            class="flex flex-col items-center justify-center gap-0.5 text-slate-600">

            <svg viewBox="0 0 24 24" class="size-6"
                fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round">

                <circle cx="12" cy="8" r="4"/>
                <path d="M5 21v-2a7 7 0 0 1 14 0v2"/>

            </svg>

            <span class="text-sm font-medium">Akun</span>
        </a>

    </div>

</nav>

{{-- FILTER JAVASCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const filterButtons = document.querySelectorAll('.filter-button');
        const gradeCards = document.querySelectorAll('.grade-card');

        filterButtons.forEach(button => {

            button.addEventListener('click', function () {

                const selectedFilter = this.dataset.filter;

                filterButtons.forEach(item => {
                    item.classList.remove('bg-blue-600', 'text-white');
                    item.classList.add('border', 'border-slate-300', 'bg-white', 'text-slate-900');
                });

                this.classList.remove('border', 'border-slate-300', 'bg-white', 'text-slate-900');
                this.classList.add('bg-blue-600', 'text-white');

                gradeCards.forEach(card => {

                    if (
                        selectedFilter === 'all' ||
                        card.dataset.subject === selectedFilter
                    ) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }

                });

            });

        });

    });
</script>

@endsection
