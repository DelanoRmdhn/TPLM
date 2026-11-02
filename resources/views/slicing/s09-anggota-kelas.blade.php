<x-templates.mobile title="Anggota Kelas" nav="guru" active="beranda">
    <x-slot:header>
        <x-molecules.top-bar title="Matematika 4-A" subtitle="Ibu Siti Rahma, S.Pd · 30 siswa" :back="route('slicing.show', 'm02b-beranda-guru')">
            <x-molecules.tabs :items="['pelajaran' => 'Pelajaran', 'forum' => 'Forum', 'anggota' => 'Anggota']" active="anggota" />
        </x-molecules.top-bar>
    </x-slot:header>

    <x-organisms.member-list manage />
</x-templates.mobile>
