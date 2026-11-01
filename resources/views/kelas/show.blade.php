{{-- M03a (guru) / M03b (siswa) Halaman kelas — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', $classroom->name)

@section('content')

    <a href="{{ route(auth()->user()->homeRoute()) }}" class="inline-flex h-12 items-center text-brand underline">← Beranda</a>

    <section class="rounded-card border border-border bg-white p-4">
        <p class="text-caption text-text-muted">{{ $classroom->subject }} · {{ $classroom->academic_year }}</p>
        <h1 class="text-h1 font-bold">{{ $classroom->name }}</h1>
        <p class="mt-1 text-text-muted">{{ $classroom->teacher->name }}</p>
        <a href="{{ route('kelas.anggota', $classroom) }}" class="mt-3 flex h-12 items-center justify-center rounded-control border border-border">Lihat Anggota</a>
    </section>

    @if ($canManage)
        <a href="{{ route('kelas.materi.create', $classroom) }}" class="flex h-12 items-center justify-center rounded-control bg-brand font-semibold text-white">+ Buat Materi</a>

        <form method="POST" action="{{ route('kelas.topik.store', $classroom) }}" class="flex gap-2">
            @csrf
            <input name="name" required maxlength="100" placeholder="Nama topik baru, mis. Bab 1: Pecahan"
                class="h-12 min-w-0 flex-1 rounded-control border border-border px-3">
            <button type="submit" class="h-12 shrink-0 rounded-control border border-brand px-4 font-semibold text-brand">Tambah Topik</button>
        </form>
    @endif

    {{-- Materi per topik, urut sesuai bab (PB-09) --}}
    @foreach ($classroom->topics as $topic)
        <section class="rounded-card border border-border bg-white p-4">
            <div class="flex items-center justify-between gap-2">
                <h2 class="font-bold">{{ $topic->name }}</h2>

                @if ($canManage)
                    <div class="flex gap-1">
                        @unless ($loop->first)
                            <form method="POST" action="{{ route('kelas.topik.move', [$classroom, $topic, 'naik']) }}">
                                @csrf
                                <button type="submit" aria-label="Naikkan topik" class="size-12 rounded-control border border-border">↑</button>
                            </form>
                        @endunless
                        @unless ($loop->last)
                            <form method="POST" action="{{ route('kelas.topik.move', [$classroom, $topic, 'turun']) }}">
                                @csrf
                                <button type="submit" aria-label="Turunkan topik" class="size-12 rounded-control border border-border">↓</button>
                            </form>
                        @endunless
                    </div>
                @endif
            </div>

            @include('kelas._materi-list', ['materials' => $topic->materials])
        </section>
    @endforeach

    @if ($withoutTopic->isNotEmpty())
        <section class="rounded-card border border-border bg-white p-4">
            <h2 class="font-bold">Tanpa topik</h2>
            @include('kelas._materi-list', ['materials' => $withoutTopic])
        </section>
    @endif

    @if ($classroom->topics->isEmpty() && $withoutTopic->isEmpty())
        <p class="rounded-card border border-border bg-white p-4 text-text-muted">Belum ada materi.</p>
    @endif

@endsection
