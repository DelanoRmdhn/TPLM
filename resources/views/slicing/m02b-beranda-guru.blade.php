<x-templates.mobile title="Beranda" nav="guru" active="beranda">
    <x-slot:header>
        <x-molecules.greeting-bar title="Halo, Ibu Siti!" subtitle="Guru · Wali Kelas 4-A" />
    </x-slot:header>

    <x-organisms.grading-queue-card count="24" detail="Dari 2 kelas · paling lama dikirim 2 hari lalu" />

    <x-atoms.button variant="outline" icon="plus" :href="route('slicing.show', 'm03-buat-kelas')">Buat Kelas Baru</x-atoms.button>

    <x-molecules.section-heading title="Kelas Saya" />
    <x-molecules.card-class title="Matematika 4-A" subtitle="30 siswa · 2026/2027" meta="12 tugas perlu dinilai" :href="route('slicing.show', 'm03b-kelas-guru')" />
    <x-molecules.card-class title="Matematika 4-B" subtitle="28 siswa · 2026/2027" meta="12 tugas perlu dinilai" tone="green" />
    <x-molecules.card-class title="Kelas Wali 4-A" subtitle="30 siswa · Wali Kelas" />
</x-templates.mobile>
