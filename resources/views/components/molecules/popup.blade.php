@props(['title', 'icon' => 'check', 'tone' => 'green', 'button' => 'Oke'])

{{-- Molekul: pop-up umpan balik tombol (frame OVERLAY). Satu tombol "Oke". --}}
<div role="dialog" aria-modal="true" aria-label="{{ $title }}" {{ $attributes->merge(['class' => 'w-full max-w-[300px] rounded-2xl bg-white p-4 shadow-[0_12px_32px_rgba(15,23,42,0.18)]']) }}>
    <x-atoms.icon-tile :icon="$icon" :tone="$tone" size="sm" />

    <h2 class="mt-3 text-lg font-bold text-slate-900">{{ $title }}</h2>

    @if ($slot->isNotEmpty())
        <div class="mt-1 text-sm leading-5 text-slate-700">{{ $slot }}</div>
    @endif

    <x-atoms.button icon="check" class="mt-3">{{ $button }}</x-atoms.button>
</div>
