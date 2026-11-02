{{-- M04b Buat/ubah materi — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', ($material->exists ? 'Ubah Materi · ' : 'Buat Materi · ').$classroom->name)

@section('content')

    <a href="{{ $material->exists ? route('kelas.materi.show', [$classroom, $material]) : route('kelas.show', $classroom) }}"
        class="inline-flex h-12 items-center text-brand underline">← Kembali</a>

    <form method="POST" enctype="multipart/form-data"
        action="{{ $material->exists ? route('kelas.materi.update', [$classroom, $material]) : route('kelas.materi.store', $classroom) }}"
        class="space-y-4 rounded-card border border-border bg-white p-4">
        @csrf
        @if ($material->exists)
            @method('PUT')
        @endif

        <div>
            <label for="title" class="mb-1 block font-semibold">Judul materi</label>
            <input id="title" name="title" value="{{ old('title', $material->title) }}" required maxlength="150"
                placeholder="Contoh: Mengenal Pecahan" class="h-12 w-full rounded-control border border-border px-3">
        </div>

        <div>
            <label for="topic_id" class="mb-1 block font-semibold">Topik</label>
            <select id="topic_id" name="topic_id" class="h-12 w-full rounded-control border border-border px-3">
                <option value="">Tanpa topik</option>
                @foreach ($topics as $topic)
                    <option value="{{ $topic->id }}" @selected(old('topic_id', $material->topic_id) == $topic->id)>{{ $topic->name }}</option>
                @endforeach
            </select>
            <input name="new_topic" value="{{ old('new_topic') }}" maxlength="100"
                placeholder="atau ketik topik baru" class="mt-2 h-12 w-full rounded-control border border-border px-3">
        </div>

        <div>
            <label for="description" class="mb-1 block font-semibold">Keterangan (boleh kosong)</label>
            <textarea id="description" name="description" rows="4" maxlength="5000"
                class="w-full rounded-control border border-border p-3">{{ old('description', $material->description) }}</textarea>
        </div>

        @if ($material->exists)
            @include('materi._lampiran-lama', ['attachments' => $material->attachments])
        @endif

        <div>
            <label for="files" class="mb-1 block font-semibold">{{ $material->exists ? 'Tambah file' : 'File' }} (PDF, gambar, DOCX · maks 10 MB per file)</label>
            <input id="files" name="files[]" type="file" multiple accept=".pdf,.jpg,.jpeg,.png,.docx"
                class="block w-full rounded-control border border-border p-3">
        </div>

        <div>
            <label for="links" class="mb-1 block font-semibold">Tautan video YouTube (satu per baris)</label>
            <textarea id="links" name="links" rows="2" placeholder="https://www.youtube.com/watch?v=..."
                class="w-full rounded-control border border-border p-3">{{ old('links') }}</textarea>
        </div>

        <button type="submit" class="h-12 w-full rounded-control bg-brand font-semibold text-white">{{ $material->exists ? 'Simpan Perubahan' : 'Bagikan Materi' }}</button>
    </form>

@endsection
