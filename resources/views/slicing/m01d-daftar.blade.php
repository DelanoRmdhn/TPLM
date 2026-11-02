<x-templates.mobile title="Daftar Akun Baru">
    <x-slot:header>
        <x-molecules.top-bar title="Daftar Akun Baru" subtitle="SDN 002 Karangmulya" :back="route('slicing.show', 'm01-login')" />
    </x-slot:header>

    <x-organisms.register-form role="siswa" />
</x-templates.mobile>
