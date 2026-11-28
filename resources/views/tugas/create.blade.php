{{-- M04 Buat/ubah tugas: judul, instruksi, tenggat, nilai maksimal, topik, lampiran (FR-16, PB-11). --}}
@extends('layouts.app')

@php($assignment ??= new \App\Models\Assignment)

@section('title', $assignment->exists ? 'Ubah Tugas' : 'Buat Tugas')
@section('back', $assignment->exists ? route('kelas.tugas.show', [$classroom, $assignment]) : route('kelas.show', $classroom))
@section('back-label', 'Kembali')

@section('content')

    <form method="POST" enctype="multipart/form-data"
        action="{{ $assignment->exists ? route('kelas.tugas.update', [$classroom, $assignment]) : route('kelas.tugas.store', $classroom) }}"
        class="space-y-5">
        @csrf
        @if ($assignment->exists)
            @method('PUT')
        @endif

        <x-card class="space-y-4">
            <p class="flex items-center gap-2 text-caption font-bold text-text-muted">
                <x-subject-icon :subject="$classroom->subject" class="size-8! rounded-[10px]! [&_svg]:size-5!" />
                {{ $classroom->name }}
            </p>

            <x-text-input name="title" label="Judul tugas" :value="$assignment->title" required maxlength="150" placeholder="Contoh: Latihan Pecahan Senilai" />

            <x-textarea name="instructions" label="Petunjuk untuk siswa" :value="$assignment->instructions" rows="4" maxlength="5000" optional
                placeholder="Contoh: Kerjakan soal buku halaman 12 nomor 1 sampai 5, lalu foto hasilnya." />

            <div class="grid gap-4 sm:grid-cols-2">
                <x-text-input name="due_at" type="datetime-local" label="Tenggat (WIB)" required
                    :value="($assignment->due_at ?? now()->addDays(3)->setTime(23, 59))->format('Y-m-d\TH:i')" class="px-3" />
                <x-text-input name="max_score" type="number" label="Nilai maksimal" required min="1" max="100" inputmode="numeric"
                    :value="$assignment->max_score ?? 100" />
            </div>
        </x-card>

        <x-card class="space-y-4">
            <h2 class="flex items-center gap-2 text-h2 font-extrabold">
                <x-icon name="bookmark-simple" class="size-6 text-brand" />
                Topik
            </h2>
            <x-select name="topic_id" label="Pilih topik">
                <option value="">Tanpa topik</option>
                @foreach ($topics as $topic)
                    <option value="{{ $topic->id }}" @selected(old('topic_id', $assignment->topic_id) == $topic->id)>{{ $topic->name }}</option>
                @endforeach
            </x-select>
            <x-text-input name="new_topic" label="Atau buat topik baru" maxlength="100" placeholder="Contoh: Bab 2: Bangun Datar" />
        </x-card>

        <x-card class="space-y-4">
            <h2 class="flex items-center gap-2 text-h2 font-extrabold">
                <x-icon name="paperclip" class="size-6 text-brand" />
                Lampiran
            </h2>

            @if ($assignment->exists)
                @include('materi._lampiran-lama', ['attachments' => $assignment->attachments])
            @endif

            <x-file-input name="files[]" :label="$assignment->exists ? 'Tambah file' : 'File'" types="pdf,jpg,jpeg,png,docx" :max-mb="10" optional multiple />

            <x-textarea name="links" label="Tautan video YouTube" rows="2" optional hint="Satu tautan per baris." placeholder="https://www.youtube.com/watch?v=..." />
        </x-card>

        <x-button :icon="$assignment->exists ? 'floppy-disk' : 'paper-plane-tilt'" size="lg" block>
            {{ $assignment->exists ? 'Simpan Perubahan' : 'Terbitkan' }}
        </x-button>
    </form>

@endsection
