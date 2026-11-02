<x-templates.mobile title="Daftar Akun Baru (Guru)">
    <x-slot:header>
        <x-molecules.top-bar title="Daftar Akun Baru" subtitle="SDN 002 Karangmulya" :back="route('slicing.show', 'm01-login')" />
    </x-slot:header>

    <x-organisms.register-form role="guru" />
</x-templates.mobile>
