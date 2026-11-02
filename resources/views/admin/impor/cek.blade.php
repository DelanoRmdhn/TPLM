{{-- M08b Hasil cek impor (sebelum Simpan) — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Cek Data Impor')

@section('content')

    @php($ready = count($check['valid']))
    @php($problemCount = count($check['problems']))

    <p class="text-text-muted">Data {{ $check['type'] }} belum disimpan. Periksa dulu hasil cek di bawah.</p>

    <section class="grid grid-cols-2 gap-3">
        <div class="rounded-card border border-green-200 bg-green-50 p-4">
            <p class="text-caption text-green-800">Siap dibuat</p>
            <p class="text-h1 font-bold text-green-800">{{ $ready }}</p>
        </div>
        <div class="rounded-card border border-red-200 bg-red-50 p-4">
            <p class="text-caption text-red-700">Baris bermasalah</p>
            <p class="text-h1 font-bold text-red-700">{{ $problemCount }}</p>
        </div>
    </section>

    @if ($problemCount > 0)
        <section class="rounded-card border border-border bg-white p-4">
            <h2 class="font-bold">Baris bermasalah (tidak dibuat)</h2>
            <ul class="mt-2 divide-y divide-border">
                @foreach ($check['problems'] as $problem)
                    <li class="py-2"><span class="font-semibold">Baris {{ $problem['row'] }}:</span> {{ $problem['message'] }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    @if ($ready > 0)
        <details class="rounded-card border border-border bg-white p-4">
            <summary class="flex min-h-12 cursor-pointer items-center font-bold">Lihat {{ $ready }} akun yang akan dibuat</summary>
            <ul class="mt-2 divide-y divide-border">
                @foreach ($check['valid'] as $row)
                    <li class="flex justify-between gap-2 py-2">
                        <span>{{ $row['name'] }}</span>
                        <span class="text-caption text-text-muted">{{ $row['username'] }}{{ $row['rombel'] ? ' · '.$row['rombel'] : '' }}</span>
                    </li>
                @endforeach
            </ul>
        </details>

        <form method="POST" action="{{ route('admin.impor.simpan') }}">
            @csrf
            <button type="submit" class="h-12 w-full rounded-control bg-brand font-semibold text-white">Simpan {{ $ready }} Akun</button>
        </form>
        <p class="text-center text-caption text-text-muted">Akun dibuat dengan kata sandi awal yang wajib diganti saat login pertama.</p>
    @else
        <p class="rounded-card border border-border bg-white p-4">Belum ada baris yang bisa dibuat. Perbaiki file Excel lalu unggah lagi.</p>
    @endif

    <a href="{{ route('admin.impor.index') }}" class="flex h-12 items-center justify-center rounded-control border border-border">Pilih File Lain</a>

@endsection
