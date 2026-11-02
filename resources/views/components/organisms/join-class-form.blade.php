{{-- Organisme: gabung kelas dengan kode (C02, siswa) --}}
<x-molecules.card class="flex flex-col items-center gap-3 py-6 text-center">
    <x-atoms.icon-tile icon="users" size="xl" />
    <h2 class="text-xl font-bold text-slate-900">Masukkan Kode Kelas</h2>
    <p class="text-base text-slate-600">Kode terdiri dari 6 huruf atau angka. Tanyakan ke gurumu.</p>

    <x-molecules.code-input value="MTK4A" />

    <x-atoms.button icon="chevron-right" class="mt-1">Gabung ke Kelas</x-atoms.button>
</x-molecules.card>

<x-molecules.notice variant="neutral" icon="info">
    Belum punya kode? Minta Wali Kelas atau Operator Sekolah mendaftarkanmu.
</x-molecules.notice>
