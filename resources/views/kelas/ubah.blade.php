{{-- Ubah, arsipkan, atau hapus kelas (guru pengajar). --}}
@extends('layouts.app')

@section('title', 'Ubah Kelas')
@section('width', 'narrow')
@section('back', route('kelas.show', $classroom))
@section('back-label', 'Kembali ke kelas')

@section('content')

    <form method="POST" action="{{ route('kelas.update', $classroom) }}" class="space-y-4 rounded-card bg-surface p-5 shadow-card">
        @csrf
        @method('PUT')

        <x-text-input name="nama_kelas" label="Nama kelas" :value="$classroom->name" required maxlength="100" />

        <x-select name="mata_pelajaran" label="Mata pelajaran" required>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" @selected((int) old('mata_pelajaran', $classroom->subject_id) === $subject->id)>{{ $subject->name }}</option>
            @endforeach
        </x-select>

        <x-select name="tahun_ajaran" label="Tahun ajaran" required>
            @foreach ($academicYears as $year)
                <option value="{{ $year }}" @selected(old('tahun_ajaran', $classroom->academic_year) === $year)>{{ $year }}</option>
            @endforeach
        </x-select>

        <x-button icon="floppy-disk" block>Simpan</x-button>
    </form>

    <x-card class="space-y-3">
        <h2 class="flex items-center gap-2 text-h2 font-extrabold">
            <x-icon name="archive" class="size-6 text-brand" />
            Arsipkan kelas
        </h2>
        <p class="text-text-muted">Kelas hilang dari beranda guru dan siswa. Materi, tugas, dan nilai tetap tersimpan, dan kelas bisa dipulihkan kapan saja.</p>
        <form method="POST" action="{{ route('kelas.archive', $classroom) }}"
            data-confirm-title="Arsipkan kelas ini?" data-confirm="{{ $classroom->name.' akan hilang dari beranda. Kamu bisa memulihkannya kapan saja.' }}" data-confirm-label="Ya, arsipkan">
            @csrf
            <x-button variant="secondary" icon="archive" block>Arsipkan Kelas</x-button>
        </form>
    </x-card>

    <x-card class="space-y-3 ring-2 ring-danger/20">
        <h2 class="flex items-center gap-2 text-h2 font-extrabold text-danger">
            <x-icon name="trash" class="size-6" />
            Hapus kelas
        </h2>
        @if ($canDelete)
            <p class="text-text-muted">Kelas beserta semua materi dan tugasnya dihapus permanen.</p>
            <form method="POST" action="{{ route('kelas.destroy', $classroom) }}"
                data-confirm-title="Hapus kelas ini?" data-confirm="{{ $classroom->name.' beserta semua materi dan tugasnya akan dihapus. Tidak bisa dibatalkan.' }}" data-confirm-label="Ya, hapus" data-confirm-tone="danger">
                @csrf
                @method('DELETE')
                <x-button variant="danger" icon="trash" block>Hapus Kelas</x-button>
            </form>
        @else
            <p class="flex items-start gap-2 rounded-control bg-background p-3 text-text-muted">
                <x-icon name="info" class="mt-0.5 size-5" />
                Kelas ini sudah punya pengumpulan atau nilai siswa, jadi tidak bisa dihapus. Arsipkan saja.
            </p>
        @endif
    </x-card>

@endsection
