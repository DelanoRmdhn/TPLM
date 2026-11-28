{{-- M04b Buat/ubah materi: judul, topik, keterangan, file & video YouTube (FR-14, FR-15, PB-09). --}}
@extends('layouts.app')

@section('title', $material->exists ? 'Ubah Materi' : 'Buat Materi')
@section('back', $material->exists ? route('kelas.materi.show', [$classroom, $material]) : route('kelas.show', $classroom))
@section('back-label', 'Kembali')

@section('content')

    <form method="POST" enctype="multipart/form-data"
        action="{{ $material->exists ? route('kelas.materi.update', [$classroom, $material]) : route('kelas.materi.store', $classroom) }}"
        class="space-y-5">
        @csrf
        @if ($material->exists)
            @method('PUT')
        @endif

        <x-card class="space-y-4">
            <p class="flex items-center gap-2 text-caption font-bold text-text-muted">
                <x-subject-icon :subject="$classroom->subject" class="size-8! rounded-[10px]! [&_svg]:size-5!" />
                {{ $classroom->name }}
            </p>

            <x-text-input name="title" label="Judul materi" :value="$material->title" required maxlength="150" placeholder="Contoh: Mengenal Pecahan" />

            <x-textarea name="description" label="Keterangan" :value="$material->description" rows="4" maxlength="5000" optional
                placeholder="Contoh: Baca dulu, lalu tonton videonya." />
        </x-card>

        <x-card class="space-y-4">
            <h2 class="flex items-center gap-2 text-h2 font-extrabold">
                <x-icon name="bookmark-simple" class="size-6 text-brand" />
                Topik
            </h2>
            <x-select name="topic_id" label="Pilih topik">
                <option value="">Tanpa topik</option>
                @foreach ($topics as $topic)
                    <option value="{{ $topic->id }}" @selected(old('topic_id', $material->topic_id) == $topic->id)>{{ $topic->name }}</option>
                @endforeach
            </x-select>
            <x-text-input name="new_topic" label="Atau buat topik baru" maxlength="100" placeholder="Contoh: Bab 2: Bangun Datar" />
        </x-card>

        <x-card class="space-y-4">
            <h2 class="flex items-center gap-2 text-h2 font-extrabold">
                <x-icon name="paperclip" class="size-6 text-brand" />
                File dan video
            </h2>

            @if ($material->exists)
                @include('materi._lampiran-lama', ['attachments' => $material->attachments])
            @endif

            <x-file-input name="files[]" :label="$material->exists ? 'Tambah file' : 'File'" types="pdf,jpg,jpeg,png,docx" :max-mb="10" multiple />

            <x-textarea name="links" label="Tautan video YouTube" rows="2" hint="Satu tautan per baris." placeholder="https://www.youtube.com/watch?v=..." />
        </x-card>

        <x-button :icon="$material->exists ? 'floppy-disk' : 'share-fat'" size="lg" block>
            {{ $material->exists ? 'Simpan Perubahan' : 'Bagikan Materi' }}
        </x-button>
    </form>

@endsection
