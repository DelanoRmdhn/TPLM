<x-templates.mobile title="Pendaftaran Berhasil">
    <section class="flex flex-col items-center gap-3 pt-16 text-center">
        <x-atoms.icon-tile icon="check" tone="green" size="xl" round />
        <h1 class="mt-2 text-h1 font-bold text-slate-900">Pendaftaran Berhasil</h1>
        <p class="text-base leading-6 text-slate-700">Akunmu sudah dibuat. Masuk memakai NISN atau NIP dan kata sandi yang tadi kamu buat.</p>
        <x-atoms.button icon="chevron-right" :href="route('slicing.show', 'm01-login')" class="mt-3">Masuk Sekarang</x-atoms.button>
    </section>
</x-templates.mobile>
