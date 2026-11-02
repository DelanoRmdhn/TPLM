<x-templates.mobile title="Pengaturan Kelas" nav="guru" active="beranda">
    <x-slot:header>
        <x-molecules.top-bar title="Pengaturan Kelas" subtitle="Matematika 4-A" :back="route('slicing.show', 'm03b-kelas-guru')" />
    </x-slot:header>

    <x-organisms.class-settings />
</x-templates.mobile>
