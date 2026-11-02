<x-templates.mobile title="Kelola Akun" nav="admin" active="pengguna">
    <x-slot:header>
        <x-molecules.top-bar title="Kelola Akun" subtitle="Operator SDN 002 Karangmulya" />
    </x-slot:header>

    <x-organisms.admin-accounts :open="true" />

    <x-slot:overlay>
        <x-molecules.bottom-sheet title="Kata Sandi Sementara">
            <p class="mt-1 text-sm text-slate-600">Untuk Ahmad Fauzi · NISN 0123984101</p>

            <p class="mt-4 rounded-xl bg-slate-100 py-4 text-center text-[32px] font-bold tracking-[0.08em] text-slate-900">KRM-4821</p>

            <x-molecules.notice variant="warning" icon="key" class="mt-4">
                Berikan kata sandi ini ke siswa. Siswa wajib menggantinya saat login berikutnya.
            </x-molecules.notice>

            <div class="mt-4 space-y-3">
                <x-atoms.button variant="outline" icon="copy">Salin Kata Sandi</x-atoms.button>
                <x-atoms.button icon="check">Selesai</x-atoms.button>
            </div>
        </x-molecules.bottom-sheet>
    </x-slot:overlay>
</x-templates.mobile>
