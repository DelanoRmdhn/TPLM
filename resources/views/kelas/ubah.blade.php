{{-- Ubah, arsipkan, hapus kelas — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Ubah Kelas · '.$classroom->name)

@section('content')

    <a href="{{ route('kelas.show', $classroom) }}" class="inline-flex h-12 items-center text-brand underline">← Kembali ke kelas</a>

    <form method="POST" action="{{ route('kelas.update', $classroom) }}" class="space-y-4 rounded-card border border-border bg-white p-4">
        @csrf
        @method('PUT')

        <h1 class="text-h1 font-bold">Ubah Kelas</h1>

        <div>
            <label for="nama_kelas" class="mb-1 block font-semibold">Nama kelas</label>
            <input id="nama_kelas" name="nama_kelas" required maxlength="100" value="{{ old('nama_kelas', $classroom->name) }}"
                class="h-12 w-full rounded-control border border-border px-3">
        </div>

        <div>
            <label for="mata_pelajaran" class="mb-1 block font-semibold">Mata pelajaran</label>
            <select id="mata_pelajaran" name="mata_pelajaran" required class="h-12 w-full rounded-control border border-border px-3">
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" @selected((int) old('mata_pelajaran', $classroom->subject_id) === $subject->id)>{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="tahun_ajaran" class="mb-1 block font-semibold">Tahun ajaran</label>
            <select id="tahun_ajaran" name="tahun_ajaran" required class="h-12 w-full rounded-control border border-border px-3">
                @foreach ($academicYears as $year)
                    <option value="{{ $year }}" @selected(old('tahun_ajaran', $classroom->academic_year) === $year)>{{ $year }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="h-12 w-full rounded-control bg-brand font-semibold text-white">Simpan</button>
    </form>

    <section class="space-y-3 rounded-card border border-border bg-white p-4">
        <h2 class="font-bold">Arsipkan kelas</h2>
        <p class="text-caption text-text-muted">Kelas hilang dari beranda guru dan siswa. Materi, tugas, dan nilai tetap tersimpan, dan kelas bisa dipulihkan kapan saja.</p>
        <form method="POST" action="{{ route('kelas.archive', $classroom) }}"
            onsubmit="return confirm(@js('Arsipkan kelas '.$classroom->name.'?'))">
            @csrf
            <button type="submit" class="h-12 w-full rounded-control border border-brand font-semibold text-brand">Arsipkan Kelas</button>
        </form>
    </section>

    <section class="space-y-3 rounded-card border border-red-200 bg-white p-4">
        <h2 class="font-bold text-danger">Hapus kelas</h2>
        @if ($canDelete)
            <p class="text-caption text-text-muted">Kelas beserta semua materi dan tugasnya dihapus permanen.</p>
            <form method="POST" action="{{ route('kelas.destroy', $classroom) }}"
                onsubmit="return confirm(@js('Hapus kelas '.$classroom->name.' beserta semua materi dan tugasnya? Tidak bisa dibatalkan.'))">
                @csrf
                @method('DELETE')
                <button type="submit" class="h-12 w-full rounded-control bg-danger font-semibold text-white">Hapus Kelas</button>
            </form>
        @else
            <p class="text-caption text-text-muted">Kelas ini sudah punya pengumpulan atau nilai siswa, jadi tidak bisa dihapus. Arsipkan saja.</p>
        @endif
    </section>

@endsection
