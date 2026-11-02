{{-- M04 Buat/ubah tugas — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@php($assignment ??= new \App\Models\Assignment)

@section('title', ($assignment->exists ? 'Ubah Tugas · ' : 'Buat Tugas · ').$classroom->name)

@section('content')

    <a href="{{ $assignment->exists ? route('kelas.tugas.show', [$classroom, $assignment]) : route('kelas.show', $classroom) }}"
        class="inline-flex h-12 items-center text-brand underline">← Kembali</a>

    <form method="POST" enctype="multipart/form-data"
        action="{{ $assignment->exists ? route('kelas.tugas.update', [$classroom, $assignment]) : route('kelas.tugas.store', $classroom) }}"
        class="space-y-4 rounded-card border border-border bg-white p-4">
        @csrf
        @if ($assignment->exists)
            @method('PUT')
        @endif

        <div>
            <label for="title" class="mb-1 block font-semibold">Judul tugas</label>
            <input id="title" name="title" value="{{ old('title', $assignment->title) }}" required maxlength="150"
                placeholder="Contoh: Latihan Pecahan Senilai" class="h-12 w-full rounded-control border border-border px-3">
        </div>

        <div>
            <label for="instructions" class="mb-1 block font-semibold">Instruksi</label>
            <textarea id="instructions" name="instructions" rows="4" maxlength="5000"
                placeholder="Contoh: Kerjakan soal buku halaman 12 nomor 1–5, lalu foto hasilnya."
                class="w-full rounded-control border border-border p-3">{{ old('instructions', $assignment->instructions) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="due_at" class="mb-1 block font-semibold">Tenggat (WIB)</label>
                <input id="due_at" name="due_at" type="datetime-local" required
                    value="{{ old('due_at', ($assignment->due_at ?? now()->addDays(3)->setTime(23, 59))->format('Y-m-d\TH:i')) }}"
                    class="h-12 w-full rounded-control border border-border px-2">
            </div>
            <div>
                <label for="max_score" class="mb-1 block font-semibold">Nilai maksimal</label>
                <input id="max_score" name="max_score" type="number" min="1" max="1000" required inputmode="numeric"
                    value="{{ old('max_score', $assignment->max_score ?? 100) }}" class="h-12 w-full rounded-control border border-border px-3">
            </div>
        </div>

        <div>
            <label for="topic_id" class="mb-1 block font-semibold">Topik</label>
            <select id="topic_id" name="topic_id" class="h-12 w-full rounded-control border border-border px-3">
                <option value="">Tanpa topik</option>
                @foreach ($topics as $topic)
                    <option value="{{ $topic->id }}" @selected(old('topic_id', $assignment->topic_id) == $topic->id)>{{ $topic->name }}</option>
                @endforeach
            </select>
            <input name="new_topic" value="{{ old('new_topic') }}" maxlength="100"
                placeholder="atau ketik topik baru" class="mt-2 h-12 w-full rounded-control border border-border px-3">
        </div>

        @if ($assignment->exists)
            @include('materi._lampiran-lama', ['attachments' => $assignment->attachments])
        @endif

        <div>
            <label for="files" class="mb-1 block font-semibold">{{ $assignment->exists ? 'Tambah lampiran' : 'Lampiran' }} (boleh kosong · PDF, gambar, DOCX · maks 10 MB)</label>
            <input id="files" name="files[]" type="file" multiple accept=".pdf,.jpg,.jpeg,.png,.docx"
                class="block w-full rounded-control border border-border p-3">
        </div>

        <div>
            <label for="links" class="mb-1 block font-semibold">Tautan video YouTube (boleh kosong, satu per baris)</label>
            <textarea id="links" name="links" rows="2" placeholder="https://www.youtube.com/watch?v=..."
                class="w-full rounded-control border border-border p-3">{{ old('links') }}</textarea>
        </div>

        <button type="submit" class="h-12 w-full rounded-control bg-brand font-semibold text-white">{{ $assignment->exists ? 'Simpan Perubahan' : 'Terbitkan' }}</button>
    </form>

@endsection
