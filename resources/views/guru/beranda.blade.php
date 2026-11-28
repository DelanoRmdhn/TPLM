{{-- Beranda guru (FR-07, FR-10, PB-06): ringkasan yang perlu dinilai, kelas yang diajar, kelas arsip. --}}
@extends('layouts.app')

@section('title', 'Beranda')
@section('header')
    @include('partials.brand')
@endsection

@section('content')

    @php($firstName = \Illuminate\Support\Str::before(auth()->user()->name.' ', ' '))

    {{-- Sapaan + yang perlu dinilai --}}
    <section class="relative overflow-hidden rounded-card bg-brand p-5 text-white shadow-card">
        <span class="absolute -top-8 -right-8 size-32 rounded-full bg-white/10" aria-hidden="true"></span>
        <div class="relative">
            <h1 class="text-h1 font-extrabold">Halo, {{ $firstName }}!</h1>
            <p class="mt-1 text-white/85">
                {{ $ungradedCount > 0 ? 'Ada pengumpulan siswa yang menunggu dinilai.' : 'Semua pengumpulan sudah dinilai. Mantap!' }}
            </p>

            @if ($ungradedCount > 0)
                <div class="mt-4 rounded-[18px] bg-white/15 p-4">
                    <p class="flex items-center gap-2 font-bold">
                        <x-icon name="list-checks" variant="fill" class="size-5 text-sunny" />
                        Perlu dinilai
                    </p>
                    <p class="mt-1 flex items-baseline gap-2">
                        <span class="text-display font-extrabold">{{ $ungradedCount }}</span>
                        <span class="font-bold">tugas siswa</span>
                    </p>
                    <p class="mt-1 text-caption text-white/85">
                        Dari {{ $ungradedPerClass->count() }} kelas. Paling lama dikirim {{ $oldestUngraded->submitted_at->diffForHumans() }}.
                    </p>
                    <x-button :href="route('kelas.tugas.nilai.edit', [$oldestUngraded->classroom_id, $oldestUngraded->assignment_id, $oldestUngraded->student_id])"
                        variant="secondary" icon="pencil-simple-line" block class="mt-3 border-0">
                        Mulai Menilai
                    </x-button>
                </div>
            @endif
        </div>
    </section>

    {{-- Kelas yang diajar --}}
    <section class="space-y-3 pt-2">
        <x-section-title icon="chalkboard-teacher">Kelas Saya</x-section-title>

        <x-button :href="route('guru.buat-kelas')" variant="secondary" icon="plus-circle" block class="lg:w-fit lg:px-8">Buat Kelas Baru</x-button>

        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($classrooms as $kelas)
                <a href="{{ route('kelas.show', $kelas) }}"
                    class="flex items-center gap-4 rounded-card bg-surface p-4 shadow-card transition active:scale-[0.99]">
                    <x-subject-icon :subject="$kelas->subject" size="lg" />
                    <div class="min-w-0 flex-1">
                        <h3 class="truncate text-[17px] font-extrabold">{{ $kelas->name }}</h3>
                        <p class="mt-0.5 flex flex-wrap items-center gap-x-3 text-caption text-text-muted">
                            <span class="inline-flex items-center gap-1"><x-icon name="users-three" class="size-4" />{{ $kelas->students_count }} siswa</span>
                            <span class="inline-flex items-center gap-1"><x-icon name="calendar-blank" class="size-4" />{{ $kelas->academic_year }}</span>
                        </p>
                        @if ($perlu = $ungradedPerClass->get($kelas->id))
                            <x-chip icon="list-checks" tone="sunny" class="mt-2">{{ $perlu }} tugas perlu dinilai</x-chip>
                        @endif
                    </div>
                    <x-icon name="caret-right" variant="bold" class="size-5 text-text-muted" />
                </a>
            @empty
                <x-card class="sm:col-span-2 xl:col-span-3">
                    <x-empty-state icon="chalkboard-teacher" title="Belum ada kelas">Tekan "Buat Kelas Baru" untuk mulai.</x-empty-state>
                </x-card>
            @endforelse
        </div>
    </section>

    {{-- Kelas yang diarsipkan --}}
    @if ($archivedClassrooms->isNotEmpty())
        <details class="group rounded-card bg-surface p-4 shadow-card">
            <summary class="flex min-h-11 cursor-pointer list-none items-center gap-2 font-extrabold">
                <x-icon name="archive" class="size-6 text-text-muted" />
                <span class="flex-1">Kelas diarsipkan ({{ $archivedClassrooms->count() }})</span>
                <x-icon name="caret-down" variant="bold" class="size-5 text-text-muted transition group-open:rotate-180" />
            </summary>
            <ul class="mt-2 space-y-2">
                @foreach ($archivedClassrooms as $kelas)
                    <li>
                        <a href="{{ route('kelas.show', $kelas) }}" class="flex min-h-14 items-center gap-3 rounded-control bg-background p-3 hover:bg-brand-50">
                            <x-subject-icon :subject="$kelas->subject" class="size-10! rounded-[12px]!" />
                            <span class="min-w-0 flex-1 truncate font-bold">{{ $kelas->name }}</span>
                            <x-chip icon="calendar-blank">{{ $kelas->academic_year }}</x-chip>
                        </a>
                    </li>
                @endforeach
            </ul>
        </details>
    @endif

@endsection
