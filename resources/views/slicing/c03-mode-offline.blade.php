<x-templates.mobile title="Mode Offline" nav="siswa" active="beranda">
    <x-slot:header>
        <div class="bg-amber-100 px-4 py-3">
            <p class="flex items-start gap-2 text-base font-semibold leading-6 text-slate-900">
                <x-atoms.icon name="wifi-off" class="mt-0.5 text-amber-700" />
                Kamu sedang offline. Materi dan tugas yang pernah dibuka tetap bisa dilihat.
            </p>
        </div>
        <x-molecules.top-bar title="Tersimpan di HP" subtitle="Bisa dibuka tanpa internet" />
    </x-slot:header>

    <x-molecules.section-heading title="Materi" />
    <x-molecules.list-row icon="file-text" title="Konsep Pecahan Senilai" subtitle="Matematika · PDF · tersimpan" href="#" />
    <x-molecules.list-row icon="file-text" tone="green" title="Bagian-Bagian Daun" subtitle="IPA · PDF · tersimpan" href="#" />
    <x-molecules.list-row icon="wifi-off" tone="slate" title="Video: Perkalian Cepat" subtitle="Butuh internet untuk membuka" muted />

    <x-molecules.section-heading title="Tugas" />
    <x-molecules.item-task title="Latihan Pecahan Senilai" subtitle="Tenggat besok, 12.00 WIB" />
</x-templates.mobile>
