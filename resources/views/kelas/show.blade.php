{{-- M03a (guru) / M03b (siswa) Halaman kelas: tugas & materi per topik (PB-09). Kontrol kelola hanya untuk guru pengajar. --}}
@extends('layouts.app')

@section('title', $classroom->name)
@section('back', route(auth()->user()->homeRoute()))
@section('back-label', 'Kembali ke Beranda')
@section('topbar', 'overlay')

{{-- Kepala kelas: blok berwarna mapel --}}
@section('hero')
    <x-subject-hero :subject="$classroom->subject">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/25 px-3 py-1 text-caption font-extrabold">
            <x-icon :name="$classroom->subject->iconName()" variant="fill" class="size-4" />
            {{ $classroom->subject->name }}
        </span>
        <h1 class="mt-3 text-display font-extrabold">{{ $classroom->name }}</h1>
        <p class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 font-semibold opacity-90">
            <span class="inline-flex items-center gap-1.5"><x-icon name="chalkboard-teacher" class="size-5" />{{ $classroom->teacher->name }}</span>
            <span class="inline-flex items-center gap-1.5"><x-icon name="calendar-blank" class="size-5" />{{ $classroom->academic_year }}</span>
        </p>
    </x-subject-hero>
@endsection

@section('content')

    {{-- Aksi kelas, menumpuk di atas hero --}}
    <x-card padding="p-3">
        @if ($canOwn)
            <div class="grid grid-cols-3 gap-2">
                <x-button variant="soft" size="sm" icon="users-three" :href="route('kelas.anggota', $classroom)" class="flex-col py-2">Anggota</x-button>
                <x-button variant="soft" size="sm" icon="chart-bar" :href="route('kelas.rekap', $classroom)" class="flex-col py-2">Rekap Nilai</x-button>
                @if ($canManage)
                    <x-button variant="soft" size="sm" icon="pencil-simple" :href="route('kelas.edit', $classroom)" class="flex-col py-2">Ubah Kelas</x-button>
                @endif
            </div>
        @else
            <x-button variant="soft" icon="users-three" :href="route('kelas.anggota', $classroom)" block>Lihat Teman Sekelas</x-button>
        @endif
    </x-card>

    {{-- Kelas diarsipkan: hanya bisa dilihat --}}
    @if ($classroom->isArchived())
        <section class="rounded-card bg-sunny-50 p-5 ring-2 ring-sunny/60">
            <p class="flex items-start gap-2 font-semibold text-[#8a5a00]">
                <x-icon name="archive" variant="fill" class="size-6" />
                Kelas ini diarsipkan pada {{ $classroom->archived_at->translatedFormat('j F Y') }}. Isinya hanya bisa dilihat.
            </p>
            @if ($canOwn)
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <form method="POST" action="{{ route('kelas.restore', $classroom) }}">
                        @csrf
                        <x-button icon="arrow-counter-clockwise" block>Pulihkan Kelas</x-button>
                    </form>
                    <form method="POST" action="{{ route('kelas.destroy', $classroom) }}"
                        data-confirm="{{ 'Hapus kelas '.$classroom->name.' beserta semua materi dan tugasnya? Tidak bisa dibatalkan.' }}" data-confirm-label="Ya, hapus" data-confirm-tone="danger">
                        @csrf
                        @method('DELETE')
                        <x-button variant="secondary" icon="trash" block class="border-danger/30 text-danger hover:bg-danger-50">Hapus Kelas</x-button>
                    </form>
                </div>
            @endif
        </section>
    @endif

    {{-- Guru: buat tugas / materi / topik --}}
    @if ($canManage)
        <div class="grid grid-cols-2 gap-3">
            <x-button :href="route('kelas.tugas.create', $classroom)" icon="clipboard-text">Buat Tugas</x-button>
            <x-button :href="route('kelas.materi.create', $classroom)" variant="secondary" icon="book-open">Buat Materi</x-button>
        </div>

        <form method="POST" action="{{ route('kelas.topik.store', $classroom) }}" class="flex gap-2">
            @csrf
            <label for="topik-baru" class="sr-only">Nama topik baru</label>
            <input id="topik-baru" name="name" required maxlength="100" placeholder="Nama topik baru"
                class="h-12 min-w-0 flex-1 rounded-control border-2 border-border bg-surface px-4 outline-none focus:border-brand focus:ring-4 focus:ring-brand-100">
            <x-button variant="soft" icon="plus" class="shrink-0">Topik</x-button>
        </form>
    @endif

    {{-- Tugas & materi per topik, urut sesuai bab (PB-09) --}}
    @foreach ($classroom->topics as $topic)
        <section class="space-y-2.5 pt-3">
            <div class="flex items-center gap-2.5">
                <span class="flex size-9 shrink-0 items-center justify-center rounded-[12px] text-caption font-extrabold {{ $classroom->subject->solidClasses() }}">{{ $loop->iteration }}</span>
                <h2 class="min-w-0 flex-1 text-h2 font-extrabold">{{ $topic->name }}</h2>

                @if ($canManage)
                    @unless ($loop->first)
                        <form method="POST" action="{{ route('kelas.topik.move', [$classroom, $topic, 'naik']) }}">
                            @csrf
                            <button type="submit" aria-label="Naikkan topik {{ $topic->name }}" class="flex size-11 items-center justify-center rounded-full text-text-muted hover:bg-brand-50">
                                <x-icon name="caret-up" variant="bold" class="size-5" />
                            </button>
                        </form>
                    @endunless
                    @unless ($loop->last)
                        <form method="POST" action="{{ route('kelas.topik.move', [$classroom, $topic, 'turun']) }}">
                            @csrf
                            <button type="submit" aria-label="Turunkan topik {{ $topic->name }}" class="flex size-11 items-center justify-center rounded-full text-text-muted hover:bg-brand-50">
                                <x-icon name="caret-down" variant="bold" class="size-5" />
                            </button>
                        </form>
                    @endunless
                @endif
            </div>

            @if ($canManage)
                <details class="group mt-1">
                    <summary class="flex min-h-11 cursor-pointer list-none items-center gap-1.5 text-caption font-bold text-brand">
                        <x-icon name="pencil-simple" class="size-4" />
                        Ubah nama atau hapus topik
                    </summary>
                    <form method="POST" action="{{ route('kelas.topik.update', [$classroom, $topic]) }}" class="mt-2 flex gap-2">
                        @csrf
                        @method('PUT')
                        <label for="topik-{{ $topic->id }}" class="sr-only">Nama topik</label>
                        <input id="topik-{{ $topic->id }}" name="name" required maxlength="100" value="{{ $topic->name }}"
                            class="h-12 min-w-0 flex-1 rounded-control border-2 border-border bg-surface px-4 outline-none focus:border-brand focus:ring-4 focus:ring-brand-100">
                        <x-button variant="soft" class="shrink-0">Simpan</x-button>
                    </form>
                    <form method="POST" action="{{ route('kelas.topik.destroy', [$classroom, $topic]) }}" class="mt-2"
                        data-confirm="{{ 'Hapus topik '.$topic->name.'? Tugas dan materinya pindah ke Tanpa topik.' }}" data-confirm-label="Ya, hapus" data-confirm-tone="danger">
                        @csrf
                        @method('DELETE')
                        <x-button variant="ghost" icon="trash" size="sm" class="text-danger hover:bg-danger-50">Hapus Topik</x-button>
                    </form>
                </details>
            @endif

            @include('kelas._materi-list', ['materials' => $topic->materials, 'assignments' => $topic->assignments])
        </section>
    @endforeach

    @if ($withoutTopic->isNotEmpty() || $assignmentsWithoutTopic->isNotEmpty())
        <section class="space-y-2.5 pt-3">
            <h2 class="flex items-center gap-2.5 text-h2 font-extrabold">
                <span class="flex size-9 shrink-0 items-center justify-center rounded-[12px] bg-surface text-text-muted shadow-card">
                    <x-icon name="folder-simple" variant="fill" class="size-5" />
                </span>
                Tanpa topik
            </h2>
            @include('kelas._materi-list', ['materials' => $withoutTopic, 'assignments' => $assignmentsWithoutTopic])
        </section>
    @endif

    @if ($classroom->topics->isEmpty() && $withoutTopic->isEmpty() && $assignmentsWithoutTopic->isEmpty())
        <x-card>
            <x-empty-state icon="books" title="Belum ada tugas atau materi">
                {{ $canManage ? 'Mulai dengan membuat topik, tugas, atau materi.' : 'Gurumu belum menambahkan apa pun. Nanti cek lagi ya.' }}
            </x-empty-state>
        </x-card>
    @endif

@endsection
