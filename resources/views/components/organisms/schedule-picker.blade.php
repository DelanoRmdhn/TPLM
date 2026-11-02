{{-- Organisme: pilih waktu terbit tugas (S07, Buat Tugas langkah 2) --}}
<x-molecules.card>
    <p class="text-base font-bold text-slate-900">Latihan Pecahan Senilai</p>
    <p class="text-sm text-slate-600">Tenggat Sabtu, 18 Okt · 12.00 · Nilai maks. 100</p>
</x-molecules.card>

<x-molecules.card class="space-y-3">
    <h2 class="text-base font-bold text-slate-900">Kapan tugas muncul untuk siswa?</h2>

    <x-molecules.radio-card name="terbit" title="Terbitkan sekarang" description="Siswa langsung melihat tugas ini" />
    <x-molecules.radio-card name="terbit" title="Jadwalkan" description="Pilih tanggal dan jam terbit" checked />

    <x-molecules.form-field label="Tanggal Terbit" name="publish_date" value="Senin, 13 Okt 2026" icon="calendar" readonly />
    <x-molecules.form-field label="Jam Terbit" name="publish_time" value="07.00 WIB" icon="clock" readonly />
</x-molecules.card>

<x-molecules.notice variant="info" icon="eye">
    Tugas tersembunyi dari siswa sampai waktu terbit. Kamu tetap bisa mengubahnya.
</x-molecules.notice>

<x-atoms.button icon="calendar">Jadwalkan Tugas</x-atoms.button>
