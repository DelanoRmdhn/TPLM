<x-templates.mobile title="Forum Kelas" nav="siswa" active="beranda">
    <x-slot:header>
        <x-molecules.top-bar title="Matematika 4-A" subtitle="Ibu Siti Rahma, S.Pd · 30 siswa" :back="route('slicing.show', 'm02a-beranda-siswa')">
            <x-molecules.tabs :items="['pelajaran' => 'Pelajaran', 'forum' => 'Forum', 'anggota' => 'Anggota']" active="forum" />
        </x-molecules.top-bar>
    </x-slot:header>

    <x-organisms.forum-post />
</x-templates.mobile>
