<x-templates.mobile title="Jadwalkan Tugas" nav="guru" active="beranda">
    <x-slot:header>
        <x-molecules.top-bar title="Buat Tugas Baru" subtitle="Matematika 4-A" :back="route('slicing.show', 'm04-buat-tugas')" />
    </x-slot:header>

    <x-organisms.schedule-picker />
</x-templates.mobile>
