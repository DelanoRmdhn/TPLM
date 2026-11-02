<x-templates.mobile title="Tugas Saya" nav="siswa" active="tugas">
    <x-slot:header>
        <x-molecules.top-bar title="Tugas Saya" subtitle="Diurutkan dari tenggat terdekat" />
    </x-slot:header>

    <x-molecules.filter-bar :items="['belum' => 'Belum selesai', 'sudah' => 'Sudah selesai']" active="belum" />

    <x-molecules.section-heading title="Besok" />
    <x-molecules.item-task title="Latihan Pecahan Senilai" subtitle="Matematika · Besok, 12.00 WIB" :href="route('slicing.show', 'm05-detail-tugas')" />

    <x-molecules.section-heading title="Minggu ini" />
    <x-molecules.item-task title="Gambar Bagian Daun" subtitle="IPA · Kamis, 23 Okt, 23.59 WIB" />

    <x-molecules.section-heading title="Lewat tenggat" tone="danger" />
    <x-molecules.item-task title="Membaca Cerita Rakyat" subtitle="Bahasa Indonesia · lewat 2 hari" status="late" status-label="Terlambat" icon="book-open" />
</x-templates.mobile>
