@props(['value', 'label', 'icon' => null, 'filled' => false, 'tone' => 'default'])

{{-- Molekul: ubin angka (Rekap Nilai: sudah / belum mengumpulkan; Ringkasan Aktivitas) --}}
@php
    $classes = match (true) {
        $filled => 'bg-blue-600 text-white',
        $tone === 'success' => 'bg-green-100 text-green-800',
        $tone === 'danger' => 'bg-red-100 text-red-700',
        default => 'border border-slate-200 bg-white text-slate-900',
    };
@endphp

<div {{ $attributes->merge(['class' => "rounded-2xl p-4 $classes"]) }}>
    @if ($icon)<x-atoms.icon :name="$icon" class="size-5 text-blue-600" />@endif
    <p class="text-[32px] font-bold leading-10">{{ $value }}</p>
    <p class="text-sm font-semibold leading-5">{{ $label }}</p>
</div>
