{{-- Kotak angka ringkasan, mis. "12 Sudah mengumpulkan". tone: brand | sunny | success | danger | neutral --}}
@props(['value', 'label', 'icon' => null, 'tone' => 'brand', 'href' => null])

@php
    $tones = [
        'brand' => 'bg-brand-50 text-brand-700',
        'sunny' => 'bg-sunny-50 text-[#8a5a00]',
        'success' => 'bg-success-50 text-success',
        'danger' => 'bg-danger-50 text-danger',
        'neutral' => 'bg-surface text-text shadow-card',
    ];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->class(['block rounded-card p-4 transition', $tones[$tone] ?? $tones['brand'], 'active:scale-[0.98]' => $href]) }}>
    @if ($icon) <x-icon :name="$icon" class="size-6" /> @endif
    <p class="mt-1 text-[30px] leading-none font-extrabold">{{ $value }}</p>
    <p class="mt-1 text-caption font-bold">{{ $label }}</p>
</{{ $tag }}>
