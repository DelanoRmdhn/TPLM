{{-- Beranda siswa (PB-08, FR-10, FR-26): sapaan, ringkasan, tenggat terdekat, tugas yang belum dikerjakan, kelas yang diikuti. --}}
@extends('layouts.app')

@section('title', 'Beranda')
@section('topbar', 'none')

@section('content')

    @php
        $user = auth()->user();
        $firstName = \Illuminate\Support\Str::before($user->name.' ', ' ');
        $totalPending = $pendingPerClass->sum();
        $hour = now()->hour;
        $greeting = match (true) {
            $hour < 11 => 'Selamat pagi,',
            $hour < 15 => 'Selamat siang,',
            $hour < 18 => 'Selamat sore,',
            default => 'Selamat malam,',
        };
    @endphp

    {{-- Sapaan --}}
    <header class="flex items-start justify-between gap-3 pt-2">
        <div class="min-w-0">
            <p class="text-text-muted">{{ $greeting }}</p>
            <h1 class="text-display font-extrabold">{{ $firstName }}!</h1>
            @if ($user->rombel)
                <span class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-surface px-3 py-1 text-caption font-bold text-text-muted shadow-card">
                    <x-icon name="student" class="size-4 text-brand" />
                    Kelas {{ $user->rombel->name }}
                </span>
            @endif
        </div>
        <a href="{{ route('notifikasi.index') }}" aria-label="Notifikasi{{ $unreadNotifications > 0 ? ', ada yang belum dibaca' : '' }}"
            class="relative flex size-12 shrink-0 items-center justify-center rounded-full bg-surface text-text shadow-card active:scale-95">
            <x-icon name="bell" variant="fill" class="size-6" />
            @if ($unreadNotifications > 0)
                <span class="absolute top-2.5 right-3 size-2.5 rounded-full bg-danger ring-2 ring-surface" aria-hidden="true"></span>
            @endif
        </a>
    </header>

    {{-- HP: satu kolom berurutan (order-*). Desktop: kiri tenggat & tugas, kanan ringkasan & kelas. --}}
    <div class="flex flex-col gap-4 lg:grid lg:grid-cols-12 lg:items-start lg:gap-8">
        <div class="contents lg:col-span-7 lg:block lg:space-y-6">
            {{-- Tenggat terdekat --}}
            @if ($nearest)
                @php($nearestSubject = $nearest->assignment->classroom->subject)
                <section class="relative order-2 overflow-hidden rounded-[26px] p-5 shadow-card lg:p-7 {{ $nearestSubject->solidClasses() }}">
                    <span class="pointer-events-none absolute -right-8 -bottom-10 size-36 rounded-full bg-white/15" aria-hidden="true"></span>
                    <span class="pointer-events-none absolute -top-4 right-4 opacity-25" aria-hidden="true">
                        <x-icon :name="$nearestSubject->iconName()" variant="fill" class="size-24" />
                    </span>

                    <p class="relative inline-flex items-center gap-1.5 rounded-full bg-white/25 px-3 py-1 text-caption font-extrabold">
                        <x-icon name="alarm" variant="fill" class="size-4" />
                        Tenggat terdekat
                    </p>
                    <h2 class="relative mt-3 text-h1 leading-tight font-extrabold lg:text-display">{{ $nearest->assignment->title }}</h2>
                    <p class="relative mt-1 font-semibold opacity-90">{{ $nearestSubject->name }}, {{ $nearest->dueLabel }}</p>

                    <a href="{{ route('kelas.tugas.show', [$nearest->assignment->classroom_id, $nearest->assignment]) }}"
                        class="relative mt-4 flex min-h-13 items-center lg:w-fit lg:px-8 justify-center gap-2 rounded-full bg-white font-extrabold text-text shadow-raised active:scale-[0.98]">
                        <x-icon name="rocket-launch" variant="fill" class="size-5 text-brand" />
                        Kerjakan Sekarang
                    </a>
                </section>
            @endif

            {{-- Belum dikerjakan: maks 3, urut tenggat terdekat --}}
            <section class="order-3 space-y-3 pt-2 lg:pt-0">
                <x-section-title :href="route('siswa.tugas')" action="Lihat semua">Belum dikerjakan</x-section-title>

                @forelse ($pending as $row)
                    <x-task-card :row="$row" in-pending-list />
                @empty
                    <x-card>
                        <x-empty-state icon="confetti" title="Hore, semua beres!">Tidak ada tugas yang belum dikerjakan.</x-empty-state>
                    </x-card>
                @endforelse
            </section>

        </div>
        <div class="contents lg:col-span-5 lg:block lg:space-y-6">
            {{-- Ringkasan --}}
            <div class="order-1 grid grid-cols-2 gap-3">
                <a href="{{ route('siswa.tugas') }}" class="relative overflow-hidden rounded-[22px] bg-mapel-amber-solid p-4 text-[#5c3d00] shadow-card active:scale-[0.98]">
                    <span class="absolute -top-3 -right-3 flex size-14 items-center justify-center rounded-full bg-white/30" aria-hidden="true">
                        <x-icon name="clipboard-text" variant="fill" class="size-6" />
                    </span>
                    <p class="pr-10 text-caption font-bold">Tugas menunggu</p>
                    <p class="mt-2 text-[34px] leading-none font-extrabold">{{ $totalPending }}</p>
                </a>
                <div class="relative overflow-hidden rounded-[22px] bg-mapel-purple-solid p-4 text-white shadow-card">
                    <span class="absolute -top-3 -right-3 flex size-14 items-center justify-center rounded-full bg-white/20" aria-hidden="true">
                        <x-icon name="books" variant="fill" class="size-6" />
                    </span>
                    <p class="pr-10 text-caption font-bold opacity-90">Kelas diikuti</p>
                    <p class="mt-2 text-[34px] leading-none font-extrabold">{{ $classrooms->count() }}</p>
                </div>
            </div>

            {{-- Kelas yang diikuti: kartu berwarna mapel --}}
            <section class="order-4 space-y-3 pt-2">
                <x-section-title>Kelas Saya</x-section-title>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-2">
                    @forelse ($classrooms as $kelas)
                        <x-subject-card :classroom="$kelas" :href="route('kelas.show', $kelas)" />
                    @empty
                        <x-card class="col-span-2 sm:col-span-3 lg:col-span-2">
                            <x-empty-state icon="books" title="Belum ada kelas">Tunggu gurumu memasukkan rombelmu ke kelas.</x-empty-state>
                        </x-card>
                    @endforelse
                </div>
            </section>

        </div>
    </div>

@endsection
