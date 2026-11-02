<x-templates.mobile title="Matematika 4-A" nav="siswa" active="beranda">
    <x-slot:header>
        <x-molecules.top-bar title="Matematika 4-A" subtitle="Ibu Siti Rahma, S.Pd · 30 siswa" :back="route('slicing.show', 'm02a-beranda-siswa')">
            <x-molecules.tabs :items="['pelajaran' => 'Pelajaran', 'forum' => 'Forum', 'anggota' => 'Anggota']" active="pelajaran" />
        </x-molecules.top-bar>
    </x-slot:header>

    <x-molecules.filter-bar :items="['semua' => 'Semua', 'materi' => 'Materi', 'tugas' => 'Tugas']" active="semua" />

    <x-molecules.section-label label="BAB 2" title="Pecahan Senilai" />
    <x-molecules.list-row icon="file-text" title="Konsep Pecahan Senilai" subtitle="Materi · PDF 2 halaman · Bisa dibuka offline" :href="route('slicing.show', 'm03c-materi-1')" />
    <x-molecules.list-row icon="clipboard-check" tone="amber" title="Latihan Pecahan Senilai" subtitle="Tugas · Tenggat besok, 12.00 WIB" :href="route('slicing.show', 'm05-detail-tugas')">
        <x-atoms.badge variant="pending" class="mt-2">Belum dikerjakan</x-atoms.badge>
    </x-molecules.list-row>

    <x-molecules.section-label label="BAB 1" title="Perkalian" />
    <x-molecules.list-row icon="video" title="Video: Perkalian Cepat" subtitle="Materi · Video YouTube" href="#" />
</x-templates.mobile>
