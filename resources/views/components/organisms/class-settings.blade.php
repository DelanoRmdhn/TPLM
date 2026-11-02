{{-- Organisme: pengaturan kelas (S08) — ubah nama, kode cadangan, arsip --}}
<x-molecules.card class="space-y-4">
    <x-molecules.form-field label="Nama Kelas" name="name" value="Matematika 4-A" />
    <x-molecules.form-field label="Mata Pelajaran" name="subject" value="Matematika" icon="chevron-right" readonly />
    <x-molecules.form-field label="Tahun Ajaran" name="year" value="2026/2027" icon="chevron-right" readonly />
    <x-atoms.button icon="check">Simpan Perubahan</x-atoms.button>
</x-molecules.card>

<x-molecules.card class="space-y-3">
    <div>
        <h2 class="text-base font-bold text-slate-900">Kode Kelas (cadangan)</h2>
        <p class="text-sm text-slate-600">Untuk siswa yang belum didaftarkan oleh guru atau admin.</p>
    </div>

    <p class="rounded-xl bg-slate-100 py-3 text-center text-[28px] font-bold tracking-[0.12em] text-slate-900">MTK4A2</p>

    <div class="flex items-center justify-between gap-3">
        <span class="text-base text-slate-900">Izinkan gabung dengan kode</span>
        <x-atoms.switch checked aria-label="Izinkan gabung dengan kode" />
    </div>

    <x-atoms.button variant="neutral" icon="refresh">Buat Kode Baru</x-atoms.button>
</x-molecules.card>

<x-atoms.button variant="danger" icon="archive">Arsipkan Kelas</x-atoms.button>
