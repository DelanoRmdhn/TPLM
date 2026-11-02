<x-templates.mobile title="Gabung Kelas" nav="siswa" active="beranda">
    <x-slot:header>
        <x-molecules.top-bar title="Gabung Kelas" subtitle="Pakai kode dari gurumu" :back="route('slicing.show', 'm02a-beranda-siswa')" />
    </x-slot:header>

    <x-organisms.join-class-form />
</x-templates.mobile>
