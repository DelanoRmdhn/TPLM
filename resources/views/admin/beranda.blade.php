{{-- Beranda admin — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Beranda Admin')

@section('content')

    <section class="rounded-card border border-border bg-white p-4">
        <h1 class="text-h1 font-bold">Halo, {{ auth()->user()->name }}!</h1>
        <p class="mt-1 text-text-muted">Admin · SDN 002 Karangmulya</p>
    </section>

    @if ($pendingTeachers > 0)
        <a href="{{ route('admin.akun.index', ['peran' => 'guru', 'status' => 'nonaktif']) }}"
            class="block rounded-card border border-amber-300 bg-amber-50 p-4">
            <p class="font-bold text-amber-900">{{ $pendingTeachers }} akun guru menunggu diaktifkan</p>
            <p class="mt-1 text-caption text-amber-800">Periksa lalu aktifkan di Kelola Akun →</p>
        </a>
    @endif

    <section aria-label="Ringkasan" class="grid grid-cols-2 gap-3">
        @foreach ([
            ['Siswa aktif', $activeStudents, route('admin.akun.index', ['peran' => 'siswa', 'status' => 'aktif'])],
            ['Guru aktif', $activeTeachers, route('admin.akun.index', ['peran' => 'guru', 'status' => 'aktif'])],
            ['Kelas berjalan', $activeClassrooms, route('admin.kelas.index')],
            ['Kelas diarsipkan', $archivedClassrooms, route('admin.kelas.index', ['status' => 'arsip'])],
        ] as [$label, $total, $url])
            <a href="{{ $url }}" class="rounded-card border border-border bg-white p-4">
                <p class="text-[28px] leading-none font-bold">{{ $total }}</p>
                <p class="mt-1 text-caption text-text-muted">{{ $label }}</p>
            </a>
        @endforeach
    </section>

    <section class="flex items-center justify-between gap-3 rounded-card border border-border bg-white p-4">
        <div>
            <p class="font-semibold">Pendaftaran akun mandiri</p>
            <p class="{{ $registrationOpen ? 'text-green-700' : 'text-text-muted' }}">Status: {{ $registrationOpen ? 'Dibuka' : 'Ditutup' }}</p>
        </div>
        <a href="{{ route('admin.akun.index') }}" class="flex h-12 items-center rounded-control border border-border px-4 font-semibold">Ubah</a>
    </section>

    <nav aria-label="Menu admin" class="grid grid-cols-2 gap-3">
        <a href="{{ route('admin.akun.index') }}" class="flex h-12 items-center justify-center rounded-control bg-brand font-semibold text-white">Kelola Akun</a>
        <a href="{{ route('admin.impor.index') }}" class="flex h-12 items-center justify-center rounded-control border border-brand font-semibold text-brand">Impor Excel</a>
        <a href="{{ route('admin.kelas.index') }}" class="flex h-12 items-center justify-center rounded-control border border-border font-semibold">Daftar Kelas</a>
        <a href="{{ route('admin.rombel.index') }}" class="flex h-12 items-center justify-center rounded-control border border-border font-semibold">Rombel</a>
        <a href="{{ route('admin.mapel.index') }}" class="flex h-12 items-center justify-center rounded-control border border-border font-semibold">Mata Pelajaran</a>
    </nav>

@endsection
