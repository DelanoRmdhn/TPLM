@props(['open' => true])

{{-- Organisme: isi halaman Kelola Akun admin (M08, M08c, M08d, M08e) --}}
<x-molecules.card-registration :open="$open" />

<x-atoms.button variant="success" icon="upload">Impor Siswa dari Excel</x-atoms.button>
<x-atoms.button variant="outline" icon="plus">Tambah Akun</x-atoms.button>
<x-atoms.button variant="neutral-outline" icon="download">Unduh Template Excel</x-atoms.button>

<div class="space-y-2">
    <x-atoms.label for="cari">Cari akun</x-atoms.label>
    <x-atoms.input id="cari" placeholder="Nama, NISN, atau NIP" icon="search" />
</div>

<x-molecules.filter-bar :items="['semua' => 'Semua', 'siswa' => 'Siswa', 'guru' => 'Guru']" active="semua" />

<x-molecules.card-account name="Ahmad Fauzi" initials="AF" subtitle="NISN 0123984101 · Rombel 4-A" />
<x-molecules.card-account name="Budi Santoso" initials="BS" subtitle="NISN 0123984102 · Rombel 4-A" />
<x-molecules.card-account name="Citra Lestari" initials="CL" subtitle="NISN 0123984103 · Rombel 4-A" status="Siswa · Nonaktif" status-variant="neutral" :active="false" />
