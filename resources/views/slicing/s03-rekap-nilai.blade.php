<x-templates.mobile title="Rekap Nilai" nav="guru" active="rekap">
    <x-slot:header>
        <x-molecules.top-bar title="Rekap Nilai" subtitle="Matematika 4-A · Ganjil 2026/2027" :back="route('slicing.show', 'm03b-kelas-guru')" />
    </x-slot:header>

    <x-atoms.button variant="success" icon="download">Ekspor ke Excel</x-atoms.button>
    <p class="text-sm leading-5 text-slate-600">Format rekap sekolah: satu baris per siswa, satu kolom per tugas.</p>

    <x-organisms.grades-table />
</x-templates.mobile>
