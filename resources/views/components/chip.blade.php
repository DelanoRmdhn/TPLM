{{--
    Label kecil berbentuk pil, pengganti teks yang dipisah titik tengah.
    Contoh: <x-chip icon="calendar-blank">2026/2027</x-chip>
    tone: neutral | brand | success | warning | danger | sunny
--}}
@props(['icon' => null, 'tone' => 'neutral'])

@php
    $tones = [
        'neutral' => 'bg-background text-text-muted',
        'brand' => 'bg-brand-50 text-brand-700',
        'success' => 'bg-success-50 text-success',
        'warning' => 'bg-warning-50 text-warning',
        'danger' => 'bg-danger-50 text-danger',
        'sunny' => 'bg-sunny-50 text-[#8a5a00]',
    ];
@endphp

<span {{ $attributes->class(['inline-flex max-w-full items-center gap-1.5 rounded-full px-3 py-1 text-caption font-bold', $tones[$tone] ?? $tones['neutral']]) }}>
    @if ($icon) <x-icon :name="$icon" class="size-4" /> @endif
    <span class="min-w-0">{{ $slot }}</span>
</span>
