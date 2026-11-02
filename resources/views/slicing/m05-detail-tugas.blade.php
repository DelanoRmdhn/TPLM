<x-templates.mobile title="Latihan Pecahan Senilai" nav="siswa" active="tugas">
    <x-slot:header>
        <x-molecules.top-bar title="Latihan Pecahan Senilai" subtitle="Matematika 4-A · Ibu Siti Rahma" :back="route('slicing.show', 'm05a-tugas-saya')" />
    </x-slot:header>

    <x-molecules.card class="space-y-3">
        <x-atoms.badge variant="pending">Belum dikerjakan</x-atoms.badge>
        <x-molecules.detail-row icon="calendar">Tenggat: Besok, 18 Okt · 12.00 WIB</x-molecules.detail-row>
        <x-molecules.detail-row icon="star">Nilai maksimal: 100</x-molecules.detail-row>
    </x-molecules.card>

    <x-molecules.card class="space-y-2">
        <h2 class="text-lg font-bold text-slate-900">Petunjuk</h2>
        <ol class="space-y-1 text-base leading-6 text-slate-800">
            <li>1. Kerjakan soal halaman 48 nomor 1–5 di buku tulis.</li>
            <li>2. Tulis nama dan nomor absen di pojok kanan atas.</li>
            <li>3. Foto pekerjaanmu dengan tombol di bawah.</li>
        </ol>
    </x-molecules.card>

    <x-molecules.list-row icon="file-text" title="Soal hal 48.pdf" subtitle="Lampiran guru · ketuk untuk membuka" href="#" />

    <h2 class="pt-2 text-lg font-bold text-slate-900">Kirim Tugasmu</h2>
    <x-atoms.button icon="camera">Foto dengan Kamera</x-atoms.button>
    <x-atoms.button variant="outline" icon="image">Pilih dari Galeri</x-atoms.button>
</x-templates.mobile>
