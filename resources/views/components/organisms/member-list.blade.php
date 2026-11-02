@props(['manage' => false])

{{-- Organisme: daftar anggota kelas (S13) --}}
<div class="flex items-center justify-between gap-3">
    <h2 class="text-base font-bold text-slate-900">30 siswa</h2>
    @if ($manage)
        <x-atoms.button variant="outline" icon="plus" :block="false" compact>Tambah Rombel</x-atoms.button>
    @endif
</div>

<x-molecules.item-person name="Ibu Siti Rahma, S.Pd" initials="SR" subtitle="Guru pengajar" badge="Guru" badge-variant="info" />
<x-molecules.item-person name="Ahmad Fauzi" initials="AF" subtitle="NISN 0123984101" />
<x-molecules.item-person name="Budi Santoso" initials="BS" subtitle="NISN 0123984102" />
<x-molecules.item-person name="Citra Lestari" initials="CL" subtitle="NISN 0123984103" />
<x-molecules.item-person name="Dewi Anggraini" initials="DA" subtitle="NISN 0123984104" />
<x-molecules.item-person name="Eka Putri" initials="EP" subtitle="NISN 0123984105" />
