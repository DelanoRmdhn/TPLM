<x-templates.mobile title="Nilai Saya" nav="siswa" active="nilai">
    <x-slot:header>
        <x-molecules.top-bar title="Nilai Saya" subtitle="Ahmad Fauzi · Kelas 4-A" />
    </x-slot:header>

    <x-organisms.grade-summary average="89" graded="4" />

    <x-molecules.filter-bar :items="['semua' => 'Semua', 'matematika' => 'Matematika', 'ipa' => 'IPA']" active="semua" />

    <x-molecules.card-grade subject="Matematika" title="Latihan Pecahan Senilai" score="92" teacher="Ibu Siti Rahma" comment="Rapi dan benar semua. Pertahankan!" date="Dinilai 17 Okt 2026" />
    <x-molecules.card-grade subject="Matematika" title="PR Perkalian Dasar" score="95" teacher="Ibu Siti Rahma" comment="Sangat teliti. Hebat!" date="Dinilai 12 Okt 2026" />
</x-templates.mobile>
