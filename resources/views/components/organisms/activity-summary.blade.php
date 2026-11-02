{{-- Organisme: ringkasan aktivitas sekolah (C01, admin) --}}
<div class="grid grid-cols-2 gap-3">
    <x-molecules.stat-tile value="12" label="Kelas aktif" icon="book-open" />
    <x-molecules.stat-tile value="48" label="Tugas dibuat" icon="clipboard-check" />
    <x-molecules.stat-tile value="86%" label="Rata-rata pengumpulan" icon="chart" />
    <x-molecules.stat-tile value="312" label="Akun aktif" icon="users" />
</div>

<section class="space-y-3">
    <h2 class="text-lg font-bold text-slate-900">Pengumpulan per kelas</h2>
    <x-molecules.progress-row title="Matematika 4-A" :percent="92" detail="6 tugas · 92% tugas dikumpulkan" />
    <x-molecules.progress-row title="Matematika 4-B" :percent="88" detail="6 tugas · 88% tugas dikumpulkan" />
    <x-molecules.progress-row title="IPA 4-A" :percent="74" detail="5 tugas · 74% tugas dikumpulkan" />
</section>
