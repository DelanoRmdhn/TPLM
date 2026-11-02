<x-templates.mobile title="Pengumpulan dan Penilaian" nav="guru" active="penilaian">
    <x-slot:header>
        <x-molecules.top-bar title="Latihan Pecahan Senilai" subtitle="Matematika 4-A · Tenggat 18 Okt, 12.00" :back="route('slicing.show', 'm03b-kelas-guru')" />
    </x-slot:header>

    <div class="grid grid-cols-2 gap-3">
        <x-molecules.stat-tile value="24" label="Sudah mengumpulkan" filled />
        <x-molecules.stat-tile value="6" label="Belum mengumpulkan" />
    </div>

    <x-organisms.submission-review />
</x-templates.mobile>
