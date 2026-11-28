{{-- Beranda admin: ringkasan akun & kelas, guru yang menunggu diaktifkan, status pendaftaran, menu kelola. --}}
@extends('layouts.app')

@section('title', 'Beranda')
@section('header')
    @include('partials.brand')
@endsection

@section('content')

    <section class="relative overflow-hidden rounded-card bg-brand p-5 text-white shadow-card">
        <span class="absolute -top-8 -right-8 size-32 rounded-full bg-white/10" aria-hidden="true"></span>
        <div class="relative flex items-center gap-4">
            <span class="flex size-14 shrink-0 items-center justify-center rounded-full bg-white/15">
                <x-icon name="shield-star" variant="fill" class="size-8 text-sunny" />
            </span>
            <div class="min-w-0">
                <h1 class="text-h1 font-extrabold">Halo, {{ \Illuminate\Support\Str::before(auth()->user()->name.' ', ' ') }}!</h1>
                <p class="text-white/85">Admin sekolah</p>
            </div>
        </div>
    </section>

    @if ($pendingTeachers > 0)
        <a href="{{ route('admin.akun.index', ['peran' => 'guru', 'status' => 'nonaktif']) }}"
            class="flex items-center gap-3 rounded-card bg-sunny-50 p-4 ring-2 ring-sunny/60 transition active:scale-[0.99]">
            <span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-sunny text-[#7a4b00]">
                <x-icon name="user-plus" variant="fill" class="size-7" />
            </span>
            <span class="min-w-0 flex-1">
                <span class="block font-extrabold">{{ $pendingTeachers }} akun guru menunggu diaktifkan</span>
                <span class="block text-caption text-[#8a5a00]">Periksa lalu aktifkan di Kelola Akun</span>
            </span>
            <x-icon name="caret-right" variant="bold" class="size-5 text-[#8a5a00]" />
        </a>
    @endif

    <section aria-label="Ringkasan" class="grid grid-cols-2 gap-3">
        <x-stat :value="$activeStudents" label="Siswa aktif" icon="student" tone="brand" :href="route('admin.akun.index', ['peran' => 'siswa', 'status' => 'aktif'])" />
        <x-stat :value="$activeTeachers" label="Guru aktif" icon="chalkboard-teacher" tone="success" :href="route('admin.akun.index', ['peran' => 'guru', 'status' => 'aktif'])" />
        <x-stat :value="$activeClassrooms" label="Kelas berjalan" icon="books" tone="sunny" :href="route('admin.kelas.index')" />
        <x-stat :value="$archivedClassrooms" label="Kelas diarsipkan" icon="archive" tone="neutral" :href="route('admin.kelas.index', ['status' => 'arsip'])" />
    </section>

    <x-card padding="p-4" class="flex items-center gap-3">
        <span @class(['flex size-12 shrink-0 items-center justify-center rounded-full', 'bg-success-50 text-success' => $registrationOpen, 'bg-background text-text-muted' => ! $registrationOpen])>
            <x-icon :name="$registrationOpen ? 'lock-key-open' : 'lock-key'" class="size-7" />
        </span>
        <span class="min-w-0 flex-1">
            <span class="block font-extrabold">Pendaftaran akun mandiri</span>
            <span class="block text-caption {{ $registrationOpen ? 'font-bold text-success' : 'text-text-muted' }}">Status: {{ $registrationOpen ? 'Dibuka' : 'Ditutup' }}</span>
        </span>
        <x-button :href="route('admin.akun.index')" variant="soft" size="sm">Ubah</x-button>
    </x-card>

    <section class="space-y-3 pt-2">
        <x-section-title icon="squares-four">Menu</x-section-title>
        <nav aria-label="Menu admin" class="grid grid-cols-2 gap-3">
            @foreach ([
                ['Kelola Akun', 'users-three', route('admin.akun.index'), 'bg-brand-100 text-brand'],
                ['Impor Excel', 'microsoft-excel-logo', route('admin.impor.index'), 'bg-success-50 text-success'],
                ['Daftar Kelas', 'chalkboard-teacher', route('admin.kelas.index'), 'bg-sunny-50 text-warning'],
                ['Rombel', 'student', route('admin.rombel.index'), 'bg-mapel-purple-soft text-mapel-purple'],
                ['Mata Pelajaran', 'books', route('admin.mapel.index'), 'bg-mapel-teal-soft text-mapel-teal'],
            ] as [$label, $icon, $url, $tone])
                <a href="{{ $url }}" class="flex min-h-24 flex-col items-start justify-between gap-2 rounded-card bg-surface p-4 shadow-card transition active:scale-[0.98]">
                    <span class="flex size-11 items-center justify-center rounded-[14px] {{ $tone }}">
                        <x-icon :name="$icon" class="size-6" />
                    </span>
                    <span class="font-extrabold">{{ $label }}</span>
                </a>
            @endforeach
        </nav>
    </section>

@endsection
