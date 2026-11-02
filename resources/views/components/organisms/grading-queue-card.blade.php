@props(['count', 'detail'])

{{-- Organisme: kartu "Perlu dinilai" di Beranda guru --}}
<section class="rounded-2xl bg-blue-100 p-4">
    <p class="flex items-center gap-2 text-sm font-semibold text-blue-700">
        <x-atoms.icon name="clipboard-check" class="size-5" />
        Perlu dinilai
    </p>

    <p class="mt-2 flex items-baseline gap-2 text-slate-900">
        <span class="text-[32px] font-bold leading-10">{{ $count }}</span>
        <span class="text-xl font-bold">tugas siswa</span>
    </p>
    <p class="mt-1 text-sm text-slate-700">{{ $detail }}</p>

    <x-atoms.button icon="chevron-right" class="mt-3">Mulai Menilai</x-atoms.button>
</section>
