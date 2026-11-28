{{-- Status tugas siswa: warna + ikon + teks, jadi tetap terbaca tanpa membedakan warna (PB-13). --}}
@props(['status'])

<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-caption font-bold', $status->badgeClass()]) }}>
    <x-icon :name="$status->icon()" variant="fill" class="size-4" />
    {{ $status->label() }}
</span>
