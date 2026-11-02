@props(['name', 'initials', 'identity', 'role'])

{{-- Organisme: profil + ganti kata sandi (S14, semua peran) --}}
<x-molecules.card class="flex flex-col items-center gap-1 text-center">
    <x-atoms.avatar :initials="$initials" size="lg" />
    <h2 class="mt-2 text-xl font-bold text-slate-900">{{ $name }}</h2>
    <p class="text-sm text-slate-700">{{ $identity }}</p>
    <p class="text-sm text-slate-600">{{ $role }}</p>
</x-molecules.card>

<x-molecules.card>
    <h2 class="mb-4 text-lg font-bold text-slate-900">Ganti Kata Sandi</h2>

    <form action="#" method="POST" class="space-y-4">
        @csrf
        <x-molecules.form-field label="Kata Sandi Lama" name="current_password" type="password" placeholder="Masukkan kata sandi lama" />
        <x-molecules.password-field label="Kata Sandi Baru" name="password" placeholder="Ketik kata sandi baru" hint="Minimal 6 huruf atau angka." :toggle="false" />
        <x-molecules.password-field label="Ulangi Kata Sandi Baru" name="password_confirmation" placeholder="Ketik ulang kata sandi baru" />
        <x-atoms.button type="submit" icon="check">Simpan Kata Sandi</x-atoms.button>
    </form>
</x-molecules.card>

<x-atoms.button variant="outline" icon="log-out">Keluar</x-atoms.button>
