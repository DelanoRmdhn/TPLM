{{-- M07 Nilai saya — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Nilai Saya')

@section('content')

    <a href="{{ route('siswa.beranda') }}" class="inline-flex h-12 items-center text-brand underline">← Beranda</a>

    <h1 class="text-h1 font-bold">Nilai Saya</h1>

    {{-- PB-16: hanya nilai milik sendiri, per kelas --}}
    @forelse ($classes as $className => $rows)
        <section class="rounded-card border border-border bg-white p-4">
            <h2 class="font-bold">{{ $className }}</h2>

            <ul class="mt-2 divide-y divide-border">
                @foreach ($rows as $row)
                    <li>
                        <a href="{{ route('kelas.tugas.show', [$row->assignment->classroom_id, $row->assignment]) }}" class="block py-3">
                            <span class="flex items-start justify-between gap-3">
                                <span class="min-w-0">
                                    <span class="block font-semibold">{{ $row->assignment->title }}</span>
                                    <span class="text-caption text-text-muted">Dinilai {{ $row->grade->updated_at->translatedFormat('j M Y') }}</span>
                                </span>
                                <span class="shrink-0 text-h1 font-bold text-green-800">
                                    {{ $row->grade->score }}<span class="text-caption font-normal text-text-muted">/{{ $row->assignment->max_score }}</span>
                                </span>
                            </span>
                            @if ($row->grade->feedback)
                                <span class="mt-2 block rounded-control bg-green-50 p-3 text-caption">💬 {{ $row->grade->feedback }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @empty
        <p class="rounded-card border border-border bg-white p-4 text-text-muted">Belum ada tugas yang dinilai.</p>
    @endforelse

@endsection
