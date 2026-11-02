<x-templates.mobile title="Tugas Diserahkan" nav="siswa" active="tugas">
    <x-slot:header>
        <x-molecules.top-bar title="Latihan Pecahan Senilai" subtitle="Matematika 4-A · Ibu Siti Rahma" :back="route('slicing.show', 'm05a-tugas-saya')" />
    </x-slot:header>

    <x-molecules.card class="space-y-3">
        <x-atoms.badge variant="submitted">Diserahkan</x-atoms.badge>
        <x-molecules.detail-row icon="check" tone="green">Dikumpulkan hari ini, 09.15 WIB</x-molecules.detail-row>
        <x-molecules.detail-row icon="calendar">Tenggat: Besok, 18 Okt · 12.00 WIB</x-molecules.detail-row>
    </x-molecules.card>

    <x-molecules.card class="space-y-3">
        <h2 class="text-base font-bold text-slate-900">Yang kamu kirim</h2>
        <div class="grid grid-cols-2 gap-3">
            <x-molecules.photo-placeholder label="Foto 1" />
            <x-molecules.photo-placeholder label="Foto 2" />
        </div>
        <p class="text-sm text-slate-600">2 foto · total 2,1 MB</p>
    </x-molecules.card>

    <x-molecules.notice variant="info" icon="clock">
        Menunggu dinilai Ibu Siti. Kamu masih bisa membatalkan dan mengirim ulang sebelum dinilai.
    </x-molecules.notice>

    <x-atoms.button variant="outline" icon="refresh">Batalkan Pengumpulan</x-atoms.button>
    <p class="text-sm leading-5 text-slate-600">Setelah dibatalkan, status kembali menjadi Belum dikerjakan.</p>
</x-templates.mobile>
