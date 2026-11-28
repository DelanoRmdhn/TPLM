{{-- Daftar kelas (admin): cari, lihat berjalan/arsip, pindahkan kelas ke guru lain. Admin tidak membuka isi kelas (PB-05). --}}
@extends('layouts.app')

@section('title', 'Daftar Kelas')

@section('content')

    {{-- Berjalan / diarsipkan --}}
    <nav class="grid grid-cols-2 gap-1 rounded-full bg-surface p-1 shadow-card" aria-label="Status kelas">
        @foreach (['' => 'Berjalan', 'arsip' => 'Diarsipkan'] as $value => $label)
            @php($active = ($value === 'arsip') === $archived)
            <a href="{{ route('admin.kelas.index', array_filter(['status' => $value, 'q' => request('q')])) }}" @if ($active) aria-current="page" @endif
                @class(['flex min-h-12 items-center justify-center rounded-full font-bold transition', 'bg-brand text-white shadow-button' => $active, 'text-text-muted' => ! $active])>
                {{ $label }}
            </a>
        @endforeach
    </nav>

    <form method="GET" class="flex gap-2">
        @if ($archived) <input type="hidden" name="status" value="arsip"> @endif
        <div class="relative flex-1">
            <label for="q" class="sr-only">Cari kelas</label>
            <x-icon name="magnifying-glass" class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-text-muted" />
            <input id="q" name="q" value="{{ request('q') }}" placeholder="Cari nama kelas atau guru"
                class="h-12 w-full rounded-control border-2 border-border bg-surface pr-4 pl-11 outline-none focus:border-brand focus:ring-4 focus:ring-brand-100">
        </div>
        <x-button variant="soft" class="shrink-0">Cari</x-button>
    </form>

    <section class="space-y-3">
        <div>
            <h1 class="text-h2 font-extrabold">{{ $archived ? 'Kelas diarsipkan' : 'Kelas berjalan' }} ({{ $classrooms->total() }})</h1>
            <p class="mt-0.5 text-caption text-text-muted">Pindahkan kelas ke guru lain kalau guru pengajarnya pindah tugas. Materi, tugas, dan nilai tetap di kelas.</p>
        </div>

        <div class="grid gap-3 lg:grid-cols-2">
        @forelse ($classrooms as $kelas)
            <article class="space-y-3 rounded-card bg-surface p-4 shadow-card">
                <div class="flex items-start gap-3">
                    <x-subject-icon :subject="$kelas->subject" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-extrabold">{{ $kelas->name }}</p>
                        <p class="mt-0.5 flex items-center gap-1.5 text-caption">
                            <x-icon name="chalkboard-teacher" class="size-4 text-text-muted" />
                            <span class="font-bold">{{ $kelas->teacher->name }}</span>
                            @unless ($kelas->teacher->is_active) <span class="font-bold text-danger">(nonaktif)</span> @endunless
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-chip :icon="$kelas->subject->iconName()" tone="brand">{{ $kelas->subject->name }}</x-chip>
                    <x-chip icon="users-three">{{ $kelas->rombel?->name ?? 'Tanpa rombel' }}</x-chip>
                    <x-chip icon="student">{{ $kelas->students_count }} siswa</x-chip>
                    <x-chip icon="calendar-blank">{{ $kelas->academic_year }}</x-chip>
                </div>

                <form method="POST" action="{{ route('admin.kelas.guru', $kelas) }}" class="flex gap-2"
                    data-confirm-title="Pindahkan kelas?" data-confirm="{{ $kelas->name.' akan diajar guru yang dipilih. Guru lama tidak bisa membukanya lagi.' }}" data-confirm-label="Ya, pindahkan">
                    @csrf
                    @method('PUT')
                    <div class="relative min-w-0 flex-1">
                        <label for="guru-{{ $kelas->id }}" class="sr-only">Guru baru untuk {{ $kelas->name }}</label>
                        <select id="guru-{{ $kelas->id }}" name="teacher_id" required
                            class="h-12 w-full appearance-none rounded-control border-2 border-border bg-surface pr-9 pl-3 outline-none focus:border-brand">
                            <option value="">Pindahkan ke guru...</option>
                            @foreach ($teachers as $teacher)
                                @continue($teacher->id === $kelas->teacher_id)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                        <x-icon name="caret-down" variant="bold" class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-text-muted" />
                    </div>
                    <x-button variant="soft" icon="arrows-left-right" class="shrink-0">Pindahkan</x-button>
                </form>
            </article>
        @empty
            <x-card>
                <x-empty-state icon="chalkboard-teacher" title="Belum ada kelas">{{ $archived ? 'Belum ada kelas yang diarsipkan.' : 'Guru belum membuat kelas.' }}</x-empty-state>
            </x-card>
        @endforelse
        </div>

        {{ $classrooms->links() }}
    </section>

@endsection
