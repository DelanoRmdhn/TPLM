@props(['icon', 'label', 'active' => false, 'href' => '#'])

{{-- Molekul: satu butir navigasi bawah (Komponen · Navigasi/Item). Label 14 px. --}}
<a
    href="{{ $href }}"
    @if ($active) aria-current="page" @endif
    class="flex min-w-0 flex-1 flex-col items-center justify-center gap-1 text-sm {{ $active ? 'font-semibold text-blue-700' : 'font-semibold text-slate-600' }}"
>
    <span class="grid h-8 w-16 place-items-center rounded-full {{ $active ? 'bg-blue-100' : '' }}">
        <x-atoms.icon :name="$icon" />
    </span>
    <span class="truncate">{{ $label }}</span>
</a>
