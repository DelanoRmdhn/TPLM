@props(['username' => '', 'usernameError' => null, 'passwordError' => null, 'action' => '#'])

{{-- Organisme: kartu masuk (M01, M01b) --}}
<x-molecules.card class="space-y-4">
    <form action="{{ $action }}" method="POST" class="space-y-4">
        @csrf

        <x-molecules.form-field
            label="NISN atau Username"
            name="username"
            placeholder="Contoh: 0123984101"
            :value="$username"
            :error="$usernameError"
            autocomplete="username"
        />

        <x-molecules.password-field
            label="Kata Sandi"
            name="password"
            placeholder="Masukkan kata sandi"
            :error="$passwordError"
            :value="$passwordError ? '••••••' : ''"
        />

        <x-atoms.button type="submit" icon="lock">Masuk</x-atoms.button>
    </form>
</x-molecules.card>
