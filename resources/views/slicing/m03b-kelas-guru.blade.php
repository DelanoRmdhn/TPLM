<x-templates.mobile title="Matematika 4-A" nav="guru" active="beranda">
    <x-slot:header>
        <x-molecules.top-bar title="Matematika 4-A" subtitle="Ibu Siti Rahma, S.Pd · 30 siswa" :back="route('slicing.show', 'm02b-beranda-guru')">
            <x-molecules.tabs :items="['pelajaran' => 'Pelajaran', 'forum' => 'Forum', 'anggota' => 'Anggota']" active="pelajaran" />
        </x-molecules.top-bar>
    </x-slot:header>

    <x-atoms.button icon="plus" :href="route('slicing.show', 'm04-buat-tugas')">Buat Tugas atau Materi</x-atoms.button>
    <x-atoms.button variant="neutral-outline" icon="pencil" :href="route('slicing.show', 's08-pengaturan-kelas')">Pengaturan Kelas</x-atoms.button>

    <x-molecules.filter-bar :items="['semua' => 'Semua', 'materi' => 'Materi', 'tugas' => 'Tugas']" active="semua" />

    <x-molecules.section-label label="BAB 2" title="Pecahan Senilai" />
    <x-molecules.list-row icon="file-text" title="Konsep Pecahan Senilai" subtitle="Materi · PDF 2 halaman · Bisa dibuka offline" :href="route('slicing.show', 'm03c-materi-1')" />
    <x-molecules.list-row icon="clipboard-check" tone="amber" title="Latihan Pecahan Senilai" subtitle="Tugas · 24 dari 30 siswa mengumpulkan" :href="route('slicing.show', 'm06-pengumpulan-guru')" />
</x-templates.mobile>
