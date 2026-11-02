<x-templates.mobile title="Buat Tugas Baru" nav="guru" active="beranda">
    <x-slot:header>
        <x-molecules.top-bar title="Buat Tugas Baru" subtitle="Matematika 4-A" :back="route('slicing.show', 'm03b-kelas-guru')">
            <x-molecules.tabs :items="['tugas' => 'Tugas', 'materi' => 'Materi']" active="tugas" />
        </x-molecules.top-bar>
    </x-slot:header>

    <x-organisms.assignment-form />
</x-templates.mobile>
