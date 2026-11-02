{{-- Organisme: lembar penilaian satu pengumpulan siswa (M06) --}}
<x-molecules.card highlight class="space-y-4">
    <x-molecules.item-person
        name="Ahmad Fauzi"
        initials="AF"
        subtitle="Dikirim 17 Okt, 14.10"
        badge="Diserahkan"
        badge-variant="submitted"
        flat
    />

    <x-molecules.photo-placeholder label="2 foto dari kamera HP" tall />

    <x-atoms.button variant="neutral" icon="eye">Lihat Foto Ukuran Penuh</x-atoms.button>

    <form action="#" method="POST" class="space-y-4">
        @csrf
        <x-molecules.form-field label="Nilai (0–100)" name="score" value="92" inputmode="numeric" />
        <x-molecules.form-field label="Umpan Balik" name="feedback" textarea>Rapi dan benar semua.</x-molecules.form-field>
        <x-atoms.button type="submit" icon="check">Simpan Nilai</x-atoms.button>
    </form>
</x-molecules.card>
