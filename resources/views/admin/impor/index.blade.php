{{-- M08 Impor Excel — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Impor Akun dari Excel')

@section('content')

    <section class="rounded-card border border-border bg-white p-4">
        <h2 class="font-bold">1. Unduh template</h2>
        <p class="mt-1 text-text-muted">Isi mulai baris ke-2. Jangan ubah urutan kolom.</p>
        <div class="mt-3 grid grid-cols-2 gap-3">
            <a href="{{ route('admin.impor.template', 'siswa') }}" class="flex h-12 items-center justify-center rounded-control border border-brand font-semibold text-brand">Template Siswa</a>
            <a href="{{ route('admin.impor.template', 'guru') }}" class="flex h-12 items-center justify-center rounded-control border border-brand font-semibold text-brand">Template Guru</a>
        </div>
        <ul class="mt-3 list-inside list-disc text-caption text-text-muted">
            <li>Siswa: Nama, NISN (10 digit), Rombel (mis. 4A)</li>
            <li>Guru: Nama, NIP atau Username</li>
        </ul>
    </section>

    <form method="POST" action="{{ route('admin.impor.store') }}" enctype="multipart/form-data"
        class="space-y-4 rounded-card border border-border bg-white p-4">
        @csrf

        <h2 class="font-bold">2. Unggah file</h2>

        <fieldset class="flex gap-3">
            <label class="flex h-12 flex-1 items-center gap-2 rounded-control border border-border px-3">
                <input type="radio" name="type" value="siswa" @checked(old('type', 'siswa') === 'siswa')> Data siswa
            </label>
            <label class="flex h-12 flex-1 items-center gap-2 rounded-control border border-border px-3">
                <input type="radio" name="type" value="guru" @checked(old('type') === 'guru')> Data guru
            </label>
        </fieldset>

        <input type="file" name="file" accept=".xlsx" required
            class="block w-full rounded-control border border-border p-3">

        <p class="text-caption text-text-muted">
            File dicek dulu. Akun baru dibuat setelah kamu melihat hasil cek dan menekan Simpan.
        </p>

        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('admin.akun.index') }}" class="flex h-12 items-center justify-center rounded-control border border-border">Kembali</a>
            <button type="submit" class="h-12 rounded-control bg-brand font-semibold text-white">Cek File</button>
        </div>
    </form>

@endsection
