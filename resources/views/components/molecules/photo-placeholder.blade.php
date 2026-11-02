@props(['label', 'tall' => false])

{{-- Molekul: tempat foto pengumpulan (foto dari kamera HP) --}}
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center gap-2 rounded-xl bg-slate-100 text-sm font-semibold text-slate-600 '.($tall ? 'h-40' : 'h-24')]) }}>
    <x-atoms.icon name="image" class="size-7 text-slate-600" />
    {{ $label }}
</div>
