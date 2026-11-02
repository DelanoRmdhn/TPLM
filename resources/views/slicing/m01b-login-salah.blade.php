<x-templates.mobile title="Masuk">
    <x-organisms.auth-brand />
    <x-organisms.login-form
        username="0123984101"
        username-error="NISN atau kata sandi salah. Periksa lagi."
        password-error="Kata sandi dibedakan huruf besar dan kecil."
    />
    <x-organisms.register-prompt :open="true" :href="route('slicing.show', 'm01d-daftar')" />
</x-templates.mobile>
