@props(['role' => 'siswa'])

{{-- Organisme: form daftar akun mandiri (M01d). Isi menyesuaikan peran: siswa → NISN + rombel, guru → NIP + mata pelajaran. --}}
<x-molecules.notice variant="info" icon="lock">
    Data yang disimpan hanya nama, nomor induk, dan kelas. Tanpa email.
</x-molecules.notice>

<x-molecules.card>
    <form action="#" method="POST" class="space-y-4">
        @csrf

        <x-molecules.role-toggle :active="$role" />

        @if ($role === 'guru')
            <x-molecules.form-field label="Nama Lengkap" name="name" placeholder="Contoh: Siti Rahma, S.Pd" />
            <x-molecules.form-field label="NIP" name="nip" placeholder="18 angka" inputmode="numeric" />
            <x-molecules.form-field label="Mata Pelajaran" name="subject" placeholder="Pilih mata pelajaran" icon="chevron-right" readonly />
        @else
            <x-molecules.form-field label="Nama Lengkap" name="name" placeholder="Contoh: Ahmad Fauzi" />
            <x-molecules.form-field label="NISN" name="nisn" placeholder="10 angka, lihat di kartu pelajar" inputmode="numeric" />
            <x-molecules.form-field label="Kelas (Rombel)" name="rombel" placeholder="Pilih kelas" icon="chevron-right" readonly />
        @endif

        <x-molecules.password-field label="Kata Sandi" name="password" placeholder="Buat kata sandi" hint="Minimal 6 huruf atau angka." :toggle="false" />
        <x-molecules.password-field label="Ulangi Kata Sandi" name="password_confirmation" placeholder="Ketik ulang kata sandi" />
    </form>
</x-molecules.card>

<x-atoms.button type="submit" icon="check">Daftar Sekarang</x-atoms.button>
