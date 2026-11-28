{{-- M08 Impor akun dari Excel (FR-05, PB-02): unduh template, unggah, cek dulu, baru simpan. --}}
@extends('layouts.app')

@section('title', 'Impor dari Excel')
@section('width', 'narrow')
@section('back', route('admin.akun.index'))
@section('back-label', 'Kembali ke Kelola Akun')

@section('content')

    <x-card class="space-y-3">
        <h2 class="flex items-center gap-2 text-h2 font-extrabold">
            <span class="flex size-8 items-center justify-center rounded-full bg-brand text-caption text-white">1</span>
            Unduh template
        </h2>
        <p class="text-text-muted">Isi mulai baris ke-2. Jangan ubah urutan kolom.</p>
        <div class="grid grid-cols-2 gap-2">
            <x-button :href="route('admin.impor.template', 'siswa')" variant="secondary" icon="download-simple">Template Siswa</x-button>
            <x-button :href="route('admin.impor.template', 'guru')" variant="secondary" icon="download-simple">Template Guru</x-button>
        </div>
        <ul class="space-y-1.5 text-caption text-text-muted">
            <li class="flex items-start gap-2"><x-icon name="student" class="mt-0.5 size-4 text-brand" />Siswa: Nama, NISN (10 digit), Rombel (mis. 4A, harus ada di menu Rombel)</li>
            <li class="flex items-start gap-2"><x-icon name="chalkboard-teacher" class="mt-0.5 size-4 text-brand" />Guru: Nama, NIP atau Username</li>
        </ul>
    </x-card>

    <form method="POST" action="{{ route('admin.impor.store') }}" enctype="multipart/form-data" class="space-y-4 rounded-card bg-surface p-5 shadow-card">
        @csrf

        <h2 class="flex items-center gap-2 text-h2 font-extrabold">
            <span class="flex size-8 items-center justify-center rounded-full bg-brand text-caption text-white">2</span>
            Unggah file
        </h2>

        <fieldset>
            <legend class="mb-1.5 font-bold">Jenis data</legend>
            <div class="grid grid-cols-2 gap-3">
                @foreach (['siswa' => ['Data siswa', 'student'], 'guru' => ['Data guru', 'chalkboard-teacher']] as $value => [$label, $icon])
                    <label class="flex min-h-16 cursor-pointer items-center justify-center gap-2 rounded-[16px] border-2 border-border font-extrabold text-text-muted transition has-checked:border-brand has-checked:bg-brand-50 has-checked:text-brand has-focus-visible:outline-3 has-focus-visible:outline-brand-200">
                        <input type="radio" name="type" value="{{ $value }}" class="sr-only" @checked(old('type', 'siswa') === $value)>
                        <x-icon :name="$icon" class="size-6" />
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </fieldset>

        <x-file-input name="file" label="File Excel" types="xlsx" :max-mb="5" required />

        <p class="flex items-start gap-2 rounded-control bg-brand-50 p-3 text-caption text-brand-800">
            <x-icon name="info" class="mt-0.5 size-5" />
            File dicek dulu. Akun baru dibuat setelah kamu melihat hasil cek dan menekan Simpan.
        </p>

        <x-button icon="magnifying-glass" size="lg" block>Cek File</x-button>
    </form>

@endsection
