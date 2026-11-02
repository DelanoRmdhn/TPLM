@props(['heading', 'page' => 1, 'total' => 2])

{{-- Organisme: pembaca materi per halaman (M03c, M03c2) --}}
<x-molecules.card muted>
    <div class="rounded-md bg-white p-4 pb-24 shadow-sm">
        <p class="text-base font-bold text-slate-900">{{ $heading }}</p>
        <div class="mt-3 space-y-2" aria-hidden="true">
            @foreach ([100, 82, 100, 66, 100, 90, 54] as $width)
                <span class="block h-1.5 rounded-full bg-slate-200" style="width: {{ $width }}%"></span>
            @endforeach
        </div>
    </div>
</x-molecules.card>

<div class="grid grid-cols-2 gap-2">
    <x-atoms.button :variant="$page <= 1 ? 'neutral' : 'neutral-outline'" icon="arrow-left" :disabled="$page <= 1">Sebelumnya</x-atoms.button>
    <x-atoms.button :variant="$page >= $total ? 'neutral' : 'primary'" icon="chevron-right" :disabled="$page >= $total">Berikutnya</x-atoms.button>
</div>

<p class="text-center text-sm text-slate-600">Halaman {{ $page }} dari {{ $total }}</p>

<x-molecules.notice variant="success" icon="check">
    Sudah tersimpan di HP. Bisa dibuka lagi tanpa internet.
</x-molecules.notice>

<x-atoms.button variant="outline" icon="download">Unduh Materi</x-atoms.button>
