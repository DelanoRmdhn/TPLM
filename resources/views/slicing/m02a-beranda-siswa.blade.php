<x-templates.mobile title="Beranda" nav="siswa" active="beranda">
    <x-slot:header>
        <x-molecules.greeting-bar title="Halo, Ahmad!" subtitle="Siswa · Kelas 4-A" />
    </x-slot:header>

    <x-organisms.nearest-deadline-card
        title="Latihan Pecahan Senilai"
        subtitle="Matematika · Besok, 18 Okt, 12.00 WIB"
        :href="route('slicing.show', 'm05-detail-tugas')"
    />

    <x-molecules.section-heading title="Belum dikerjakan" action="Lihat Semua" :action-href="route('slicing.show', 'm05a-tugas-saya')" />
    <x-molecules.item-task title="Gambar Bagian Daun" subtitle="IPA · Kamis, 20 Okt, 23.59 WIB" />

    <x-molecules.section-heading title="Kelas Saya" />
    <x-molecules.card-class title="Matematika 4-A" subtitle="Ibu Siti Rahma, S.Pd" meta="1 tugas belum dikerjakan" :href="route('slicing.show', 'm03a-kelas-siswa')" />
    <x-molecules.card-class title="IPA 4-A" subtitle="Pak Joko Anwar, S.Pd" tone="green" />
</x-templates.mobile>
