<x-templates.mobile title="Kelola Akun" nav="admin" active="pengguna">
    <x-slot:header>
        <x-molecules.top-bar title="Kelola Akun" subtitle="Operator SDN 002 Karangmulya" />
    </x-slot:header>

    <x-organisms.admin-accounts :open="true" />

    <x-slot:overlay>
        <x-molecules.bottom-sheet>
            <div class="flex items-center gap-3">
                <x-atoms.icon-tile icon="user-x" tone="red" size="sm" />
                <h2 class="text-xl font-bold leading-tight text-slate-900">Nonaktifkan akun Budi Santoso?</h2>
            </div>

            <p class="mt-3 text-base leading-6 text-slate-700">Budi tidak bisa login lagi. Tugas dan nilainya tetap tersimpan, dan akun bisa diaktifkan lagi kapan saja.</p>

            <div class="mt-4 space-y-3">
                <x-atoms.button variant="danger" icon="user-x">Ya, Nonaktifkan</x-atoms.button>
                <x-atoms.button variant="neutral" icon="x">Batal</x-atoms.button>
            </div>
        </x-molecules.bottom-sheet>
    </x-slot:overlay>
</x-templates.mobile>
