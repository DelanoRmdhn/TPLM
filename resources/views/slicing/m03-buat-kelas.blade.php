<x-templates.mobile title="Buat Kelas Baru" nav="guru" active="beranda">
    <x-slot:header>
        <x-molecules.top-bar title="Buat Kelas Baru" subtitle="Kelas berisi materi, tugas, dan nilai" :back="route('slicing.show', 'm02b-beranda-guru')" />
    </x-slot:header>

    <form action="#" method="POST" class="space-y-4">
        @csrf

        <x-molecules.card class="space-y-4">
            <x-molecules.form-field label="Nama Kelas" name="name" value="Matematika 4-A" />
            <x-molecules.form-field label="Mata Pelajaran" name="subject" value="Matematika" icon="chevron-right" readonly />
            <x-molecules.form-field label="Rombel" name="rombel" value="4-A · 30 siswa" icon="chevron-right" readonly />
            <x-molecules.form-field label="Tahun Ajaran" name="year" value="2026/2027" icon="chevron-right" readonly />
        </x-molecules.card>

        <x-molecules.notice variant="success" icon="users">
            Semua 30 siswa rombel 4-A otomatis masuk ke kelas ini. Tidak perlu menambahkan satu per satu.
        </x-molecules.notice>

        <x-atoms.button type="submit" icon="check">Buat Kelas</x-atoms.button>

        <p class="text-sm leading-5 text-slate-600">Siswa pindahan bisa bergabung memakai kode kelas dari guru.</p>
    </form>
</x-templates.mobile>
