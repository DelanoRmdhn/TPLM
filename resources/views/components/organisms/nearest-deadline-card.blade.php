@props(['title', 'subtitle', 'href' => '#'])

{{-- Organisme: kartu "Tenggat terdekat" di Beranda siswa. Tugas terbuka dalam 1–2 ketukan (NFR-06). --}}
<section class="rounded-2xl bg-amber-100 p-4">
    <p class="flex items-center gap-2 text-sm font-semibold text-amber-700">
        <x-atoms.icon name="clock" class="size-5" />
        Tenggat terdekat
    </p>

    <h2 class="mt-2 text-xl font-bold leading-tight text-slate-900">{{ $title }}</h2>
    <p class="mt-1 text-sm text-slate-700">{{ $subtitle }}</p>

    <x-atoms.button icon="chevron-right" :href="$href" class="mt-3">Kerjakan Sekarang</x-atoms.button>
</section>
