{{-- S13 Anggota kelas (FR-09, FR-11, PB-07). Guru pengajar bisa menambah rombel/siswa dan mengeluarkan siswa. --}}
@extends('layouts.app')

@section('title', 'Anggota Kelas')
@section('back', route('kelas.show', $classroom))
@section('back-label', 'Kembali ke kelas')

@section('content')

    <x-card>
        <div class="flex items-center gap-4">
            <x-subject-icon :subject="$classroom->subject" />
            <div class="min-w-0 flex-1">
                <h1 class="truncate text-h1 font-extrabold">{{ $classroom->name }}</h1>
                <p class="mt-0.5 flex items-center gap-1.5 text-text-muted">
                    <x-icon name="chalkboard-teacher" class="size-5" />
                    {{ $classroom->teacher->name }}
                </p>
            </div>
        </div>
    </x-card>

    @if ($canManage)
        <div class="grid gap-3 sm:grid-cols-2">
            <form method="POST" action="{{ route('kelas.anggota.rombel', $classroom) }}" class="space-y-3 rounded-card bg-surface p-5 shadow-card">
                @csrf
                <x-select name="rombel" label="Tambah satu rombel" hint="Semua siswa rombel itu masuk sekaligus. Yang sudah ada tidak dobel." required>
                    <option value="">Pilih rombel</option>
                    @foreach ($rombels as $rombel)
                        <option value="{{ $rombel->id }}">{{ $rombel->name }} ({{ $rombel->students_count }} siswa)</option>
                    @endforeach
                </x-select>
                <x-button icon="users-three" block>Tambahkan Rombel</x-button>
            </form>

            <form method="POST" action="{{ route('kelas.anggota.store', $classroom) }}" class="space-y-3 rounded-card bg-surface p-5 shadow-card">
                @csrf
                <x-text-input name="nisn" label="Tambah satu siswa" placeholder="NISN siswa" hint="Mis. siswa pindahan atau dari rombel lain." required inputmode="numeric" maxlength="50" />
                <x-button variant="secondary" icon="user-plus" block>Tambahkan Siswa</x-button>
            </form>
        </div>
    @endif

    <x-card padding="p-4">
        <h2 class="flex items-center gap-2 text-h2 font-extrabold">
            <x-icon name="users-three" class="size-6 text-brand" />
            Siswa ({{ $students->count() }})
        </h2>

        <ul class="mt-3 space-y-2">
            @forelse ($students as $student)
                <li class="flex min-h-14 items-center gap-3 rounded-control bg-background p-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand-100 text-caption font-extrabold text-brand">
                        {{ mb_strtoupper(mb_substr($student->name, 0, 1)) }}
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate font-bold">{{ $student->name }}</span>
                        {{-- NISN hanya untuk guru (NFR-10) --}}
                        @if ($canManage)
                            <span class="mt-0.5 flex flex-wrap gap-x-3 text-caption text-text-muted">
                                <span class="inline-flex items-center gap-1"><x-icon name="identification-card" class="size-4" />{{ $student->username }}</span>
                                @if ($student->rombel)
                                    <span class="inline-flex items-center gap-1"><x-icon name="student" class="size-4" />{{ $student->rombel->name }}</span>
                                @endif
                            </span>
                        @endif
                    </span>
                    @if ($canManage)
                        <form method="POST" action="{{ route('kelas.anggota.destroy', [$classroom, $student]) }}"
                            data-confirm-title="Keluarkan dari kelas?" data-confirm="{{ $student->name.' tidak bisa membuka kelas ini lagi. Pengumpulan dan nilainya tetap tersimpan.' }}" data-confirm-label="Ya, keluarkan" data-confirm-tone="danger">
                            @csrf
                            @method('DELETE')
                            <button type="submit" aria-label="Keluarkan {{ $student->name }}" class="flex size-11 items-center justify-center rounded-full text-danger hover:bg-danger-50">
                                <x-icon name="user-minus" class="size-6" />
                            </button>
                        </form>
                    @endif
                </li>
            @empty
                <li class="rounded-control bg-background p-3 text-text-muted">Belum ada siswa.</li>
            @endforelse
        </ul>
    </x-card>

@endsection
