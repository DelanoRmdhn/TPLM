@props(['initials', 'size' => 'md'])

{{-- Atom: avatar inisial --}}
@php
    $sizes = [
        'xs' => 'size-9 text-xs',
        'sm' => 'size-10 text-sm',
        'md' => 'size-12 text-base',
        'lg' => 'size-20 text-2xl',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'grid shrink-0 place-items-center rounded-full bg-blue-100 font-semibold text-blue-700 '.($sizes[$size] ?? $sizes['md'])]) }}>{{ $initials }}</span>
