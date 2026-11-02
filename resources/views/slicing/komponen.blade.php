@php
    $icons = ['home', 'clipboard-check', 'star', 'user', 'bell', 'arrow-left', 'x', 'camera', 'image', 'file-text', 'book-open', 'check', 'clock', 'send', 'message-square', 'users', 'download', 'upload', 'key', 'search', 'plus', 'lock', 'eye', 'log-out', 'calendar', 'megaphone', 'chart', 'alert-circle', 'info', 'wifi-off', 'graduation-cap', 'check-square', 'paperclip', 'video', 'chevron-right', 'trash', 'user-x', 'refresh', 'archive', 'smartphone', 'pencil'];
@endphp

<x-templates.mobile title="Komponen">
    <x-slot:header>
        <x-molecules.top-bar title="Komponen" subtitle="Atoms, molecules, dan organisms" :back="route('slicing.index')" />
    </x-slot:header>

    <section class="space-y-3">
        <h2 class="text-lg font-bold">Atoms · Ikon</h2>
        <div class="grid grid-cols-6 gap-3 rounded-2xl border border-slate-200 bg-white p-4">
            @foreach ($icons as $name)
                <x-atoms.icon :name="$name" :title="$name" />
            @endforeach
        </div>
    </section>

    <section class="space-y-3">
        <h2 class="text-lg font-bold">Atoms · Tombol</h2>
        <x-atoms.button icon="plus">Primer</x-atoms.button>
        <x-atoms.button variant="outline" icon="plus">Garis tepi</x-atoms.button>
        <x-atoms.button variant="success" icon="plus">Hijau</x-atoms.button>
        <x-atoms.button variant="danger" icon="plus">Bahaya</x-atoms.button>
        <x-atoms.button variant="neutral" icon="plus">Netral</x-atoms.button>
    </section>

    <section class="space-y-3">
        <h2 class="text-lg font-bold">Atoms · Status</h2>
        <div class="flex flex-wrap gap-2">
            <x-atoms.badge variant="pending">Belum dikerjakan</x-atoms.badge>
            <x-atoms.badge variant="submitted">Diserahkan</x-atoms.badge>
            <x-atoms.badge variant="late">Terlambat</x-atoms.badge>
            <x-atoms.badge variant="graded">Dinilai</x-atoms.badge>
            <x-atoms.badge variant="info">Info</x-atoms.badge>
            <x-atoms.badge variant="neutral">Netral</x-atoms.badge>
        </div>
    </section>

    <section class="space-y-3">
        <h2 class="text-lg font-bold">Atoms · Kolom isian, kotak centang, sakelar</h2>
        <x-molecules.form-field label="Label" name="a" placeholder="Isi kolom" />
        <x-molecules.form-field label="Label" name="b" placeholder="Isi kolom" error="Pesan bantuan" />
        <x-atoms.checkbox>Label</x-atoms.checkbox>
        <x-atoms.checkbox checked>Label</x-atoms.checkbox>
        <div class="flex items-center gap-3"><x-atoms.switch checked aria-label="Aktif" /><x-atoms.switch aria-label="Nonaktif" /></div>
    </section>

    <section class="space-y-3">
        <h2 class="text-lg font-bold">Molecules · Filter</h2>
        <x-molecules.filter-bar :items="['semua' => 'Semua', 'materi' => 'Materi', 'tugas' => 'Tugas']" active="materi" />
    </section>

    <section class="space-y-3">
        <h2 class="text-lg font-bold">Molecules · Kartu</h2>
        <x-molecules.card-class title="Matematika 4-A" subtitle="Ibu Siti Rahma, S.Pd" meta="1 tugas belum dikerjakan" />
        <x-molecules.item-task title="Latihan Pecahan Senilai" subtitle="Tenggat: Besok, 12.00 WIB" />
        <x-molecules.item-person name="Ahmad Fauzi" initials="AF" subtitle="NISN 0123984101" />
        <x-molecules.card-grade subject="Matematika" title="Latihan Pecahan Senilai" score="92" teacher="Ibu Siti Rahma" comment="Rapi dan benar semua. Pertahankan!" date="Dinilai 17 Okt 2026" />
        <x-molecules.card-account name="Ahmad Fauzi" initials="AF" subtitle="NISN 0123984101 · Rombel 4-A" />
        <x-molecules.item-notification title="Tugas baru" body="Latihan Pecahan Senilai · Matematika" time="07.30" />
        <x-molecules.card-registration :open="true" />
        <x-molecules.card-registration :open="false" />
    </section>

    <section class="space-y-3 pb-4">
        <h2 class="text-lg font-bold">Organisms · Navigasi bawah</h2>
        <p class="text-sm text-slate-600">Dipakai di setiap halaman: siswa (5), guru (5), admin (3).</p>
    </section>
</x-templates.mobile>
