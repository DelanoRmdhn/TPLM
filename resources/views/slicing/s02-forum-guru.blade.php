<x-templates.mobile title="Forum Kelas (Guru)" nav="guru" active="beranda">
    <x-slot:header>
        <x-molecules.top-bar title="Matematika 4-A" subtitle="Ibu Siti Rahma, S.Pd · 30 siswa" :back="route('slicing.show', 'm02b-beranda-guru')">
            <x-molecules.tabs :items="['pelajaran' => 'Pelajaran', 'forum' => 'Forum', 'anggota' => 'Anggota']" active="forum" />
        </x-molecules.top-bar>
    </x-slot:header>

    <x-molecules.card class="space-y-3">
        <div class="flex items-center gap-3">
            <x-atoms.avatar initials="SR" size="sm" />
            <div class="min-w-0 flex-1">
                <x-atoms.input placeholder="Tulis pengumuman..." aria-label="Tulis pengumuman" />
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <x-atoms.button variant="neutral-outline" icon="paperclip">Lampiran</x-atoms.button>
            <x-atoms.button icon="send">Kirim</x-atoms.button>
        </div>
    </x-molecules.card>

    <x-organisms.forum-post moderate />
</x-templates.mobile>
