{{-- Organisme: form tambah materi (M04b) --}}
<form action="#" method="POST" class="space-y-4">
    @csrf

    <x-molecules.card class="space-y-4">
        <x-molecules.form-field label="Judul Materi" name="title" value="Konsep Pecahan Senilai" />
        <x-molecules.form-field label="Topik / Bab" name="topic" value="Bab 2 · Pecahan Senilai" icon="chevron-right" readonly />
        <x-molecules.form-field label="Keterangan untuk Siswa" name="description" textarea>Baca halaman 1 dan 2, lalu kerjakan latihan.</x-molecules.form-field>
    </x-molecules.card>

    <x-molecules.card class="space-y-3">
        <h2 class="text-base font-bold text-slate-900">File Materi</h2>
        <x-molecules.list-row icon="file-text" title="Konsep Pecahan.pdf" subtitle="PDF · 1,2 MB" />
        <x-molecules.list-row icon="video" title="Video: Pecahan Senilai" subtitle="Tautan YouTube" />
        <x-atoms.button variant="outline" icon="paperclip">Tambah File</x-atoms.button>
    </x-molecules.card>

    <x-atoms.button type="submit" icon="check">Simpan Materi</x-atoms.button>
</form>
