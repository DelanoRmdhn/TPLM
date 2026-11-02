@props([
    'variant' => 'primary',
    'icon' => null,
    'href' => null,
    'type' => 'button',
    'block' => true,
    'compact' => false,
])

{{-- Atom: tombol (Komponen · Tombol). Tinggi 52 px (compact 44 px), ikon selalu disertai teks. --}}
@php
    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700',
        'outline' => 'border-[1.5px] border-blue-600 bg-white text-blue-600 hover:bg-blue-50',
        'success' => 'bg-green-700 text-white hover:bg-green-800',
        'danger' => 'border border-red-600 bg-red-100 text-red-700 hover:bg-red-200',
        'neutral' => 'bg-slate-100 text-slate-800 hover:bg-slate-200',
        'neutral-outline' => 'border border-slate-300 bg-white text-slate-800 hover:bg-slate-50',
    ];

    $classes = 'inline-flex items-center justify-center gap-2 rounded-[10px] px-4 text-base font-semibold transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-50 '
        .($compact ? 'h-11 ' : 'h-[52px] ')
        .($block ? 'w-full ' : '')
        .($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-atoms.icon :name="$icon" />@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-atoms.icon :name="$icon" />@endif
        {{ $slot }}
    </button>
@endif
