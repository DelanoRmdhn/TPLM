{{-- Organisme: form buat tugas baru, langkah 1 (M04) --}}
<form action="#" method="POST" class="space-y-4">
    @csrf

    <x-molecules.card class="space-y-4">
        <x-molecules.form-field label="Judul Tugas" name="title" value="Latihan Pecahan Senilai" />
        <x-molecules.form-field label="Topik / Bab" name="topic" value="Bab 2 · Pecahan Senilai" icon="chevron-right" readonly />
        <x-molecules.form-field label="Petunjuk untuk Siswa" name="instructions" textarea :rows="4">Kerjakan soal halaman 48 nomor 1–5 di buku tulis. Foto hasilnya, lalu kirim di sini.</x-molecules.form-field>
        <x-molecules.form-field label="Tenggat" name="due" value="Sabtu, 18 Okt · 12.00 WIB" icon="calendar" readonly />
        <x-molecules.form-field label="Nilai Maksimal" name="max_score" value="100" inputmode="numeric" />
    </x-molecules.card>

    <x-atoms.button type="submit" icon="chevron-right">Lanjut</x-atoms.button>
</form>
