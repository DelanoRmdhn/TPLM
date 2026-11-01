{{-- M08b Hasil impor — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Hasil Impor')

@section('content')

    <section class="grid grid-cols-2 gap-3">
        <div class="rounded-card border border-green-200 bg-green-50 p-4">
            <p class="text-caption text-green-800">Akun dibuat</p>
            <p class="text-h1 font-bold text-green-800">{{ $result['created_count'] }}</p>
        </div>
        <div class="rounded-card border border-red-200 bg-red-50 p-4">
            <p class="text-caption text-red-700">Baris bermasalah</p>
            <p class="text-h1 font-bold text-red-700">{{ count($result['problems']) }}</p>
        </div>
    </section>

    @if ($result['created_count'] > 0)
        <section class="rounded-card border border-border bg-white p-4">
            <p>Unduh daftar akun beserta kata sandi awal untuk dibagikan ke {{ $result['type'] === 'siswa' ? 'wali kelas' : 'guru' }}.</p>
            <a href="{{ route('admin.impor.unduh') }}" class="mt-3 flex h-12 items-center justify-center rounded-control bg-brand font-semibold text-white">
                Unduh Daftar Akun &amp; Kata Sandi
            </a>
        </section>
    @endif

    @if (count($result['problems']) > 0)
        <section class="rounded-card border border-border bg-white p-4">
            <h2 class="font-bold">Baris bermasalah</h2>
            <ul class="mt-2 divide-y divide-border">
                @foreach ($result['problems'] as $problem)
                    <li class="py-2"><span class="font-semibold">Baris {{ $problem['row'] }}:</span> {{ $problem['message'] }}</li>
                @endforeach
            </ul>
            <p class="mt-2 text-caption text-text-muted">Perbaiki baris di atas di file Excel, lalu impor lagi. Baris yang sudah berhasil akan dilaporkan "sudah terdaftar".</p>
        </section>
    @endif

    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('admin.impor.index') }}" class="flex h-12 items-center justify-center rounded-control border border-border">Impor Lagi</a>
        <a href="{{ route('admin.akun.index') }}" class="flex h-12 items-center justify-center rounded-control border border-border">Ke Kelola Akun</a>
    </div>

@endsection
