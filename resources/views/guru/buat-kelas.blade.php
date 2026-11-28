{{-- Buat kelas (FR-07, PB-07): semua siswa aktif rombel yang dipilih otomatis masuk. --}}
@extends('layouts.app')

@section('title', 'Buat Kelas Baru')
@section('width', 'narrow')
@section('back', route('guru.beranda'))
@section('back-label', 'Kembali ke Beranda')

@section('content')

    <form id="form-kelas" action="{{ route('guru.kelas.store') }}" method="POST" class="space-y-4 rounded-card bg-surface p-5 shadow-card">
        @csrf

        <p class="text-text-muted">Kelas berisi materi, tugas, dan nilai untuk satu mata pelajaran.</p>

        <x-text-input name="nama_kelas" label="Nama kelas" placeholder="Contoh: Matematika 4A" required maxlength="100" />

        <x-select name="mata_pelajaran" label="Mata pelajaran" required>
            @forelse ($subjects as $subject)
                <option value="{{ $subject->id }}" @selected((int) old('mata_pelajaran') === $subject->id)>{{ $subject->name }}</option>
            @empty
                <option value="" disabled selected>Belum ada mata pelajaran. Hubungi admin.</option>
            @endforelse
        </x-select>

        <x-select name="rombel" label="Rombel" required>
            @forelse ($rombels as $rombel)
                <option value="{{ $rombel->id }}" data-name="{{ $rombel->name }}" data-count="{{ $rombel->students_count }}" @selected(old('rombel') == $rombel->id)>
                    {{ $rombel->name }} ({{ $rombel->students_count }} siswa)
                </option>
            @empty
                <option value="" disabled selected>Belum ada rombel. Minta admin menambahkan rombel.</option>
            @endforelse
        </x-select>

        <x-select name="tahun_ajaran" label="Tahun ajaran" required>
            @foreach ($academicYears as $year)
                <option value="{{ $year }}" @selected(old('tahun_ajaran') === $year)>{{ $year }}</option>
            @endforeach
        </x-select>

        <p class="flex items-start gap-3 rounded-control bg-success-50 p-4 text-text">
            <x-icon name="users-three" variant="fill" class="size-6 text-success" />
            <span>
                Semua <strong id="jumlah-siswa">0</strong> siswa rombel <strong id="nama-rombel">-</strong> otomatis masuk ke kelas ini.
                Siswa pindahan bisa ditambahkan lewat NISN di halaman Anggota.
            </span>
        </p>

        <x-button icon="check-circle" size="lg" block>Buat Kelas</x-button>
    </form>

    <script>
        (() => {
            const rombel = document.getElementById('rombel');
            const tampilkan = () => {
                const pilihan = rombel.selectedOptions[0];
                document.getElementById('jumlah-siswa').textContent = pilihan?.dataset.count ?? 0;
                document.getElementById('nama-rombel').textContent = pilihan?.dataset.name ?? '-';
            };
            rombel.addEventListener('change', tampilkan);
            tampilkan();
        })();
    </script>

@endsection
