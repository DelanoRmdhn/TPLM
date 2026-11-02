<x-templates.mobile title="Tambah Materi" nav="guru" active="beranda">
    <x-slot:header>
        <x-molecules.top-bar title="Tambah Materi" subtitle="Matematika 4-A" :back="route('slicing.show', 'm03b-kelas-guru')">
            <x-molecules.tabs :items="['tugas' => 'Tugas', 'materi' => 'Materi']" active="materi" />
        </x-molecules.top-bar>
    </x-slot:header>

    <x-organisms.material-form />
</x-templates.mobile>
