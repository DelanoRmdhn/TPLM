{{-- Organisme: hasil impor Excel (M08b) --}}
<div class="grid grid-cols-2 gap-3">
    <x-molecules.stat-tile value="28" label="akun berhasil dibuat" tone="success" />
    <x-molecules.stat-tile value="2" label="baris perlu diperbaiki" tone="danger" />
</div>

<section class="space-y-3">
    <h2 class="text-base font-bold text-slate-900">Perlu diperbaiki</h2>
    <x-molecules.error-row row="Baris 12" message="NISN kosong. Isi NISN siswa lalu impor ulang." />
    <x-molecules.error-row row="Baris 19" message="NISN 0123984102 sudah dipakai oleh Budi Santoso." />
</section>

<x-molecules.notice variant="info" icon="check">
    28 siswa sudah bisa login memakai NISN dan kata sandi awal.
</x-molecules.notice>

<x-atoms.button icon="download">Unduh Daftar Kata Sandi Awal</x-atoms.button>
<x-atoms.button variant="outline" icon="refresh">Perbaiki dan Impor Ulang</x-atoms.button>
