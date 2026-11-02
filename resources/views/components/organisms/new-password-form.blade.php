{{-- Organisme: form kata sandi baru setelah direset (M01c) --}}
<x-molecules.notice variant="warning" icon="key">
    Kata sandimu baru saja direset oleh Operator Sekolah. Buat kata sandi baru supaya akunmu aman.
</x-molecules.notice>

<x-molecules.card>
    <form action="#" method="POST" class="space-y-4">
        @csrf

        <x-molecules.form-field label="Kata Sandi Sementara" name="current_password" type="password" placeholder="Dari Operator Sekolah" />

        <x-molecules.password-field
            label="Kata Sandi Baru"
            name="password"
            placeholder="Ketik kata sandi baru"
            hint="Minimal 6 huruf atau angka."
            :toggle="false"
        />

        <x-molecules.password-field label="Ulangi Kata Sandi Baru" name="password_confirmation" placeholder="Ketik ulang kata sandi baru" />
    </form>
</x-molecules.card>

<x-atoms.button type="submit" icon="check">Simpan dan Lanjutkan</x-atoms.button>
