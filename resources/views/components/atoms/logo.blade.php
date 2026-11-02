@props(['size' => 'md'])

{{-- Atom: logo SDN 002 Karangmulya. Ganti isinya dengan logo resmi (NFR-07). --}}
@php
    $sizes = [
        'md' => 'size-12 rounded-xl',
        'lg' => 'size-[72px] rounded-[18px]',
    ];
    $icon = ['md' => 'size-7', 'lg' => 'size-10'];
@endphp

<span {{ $attributes->merge(['class' => 'grid shrink-0 place-items-center bg-blue-600 text-white '.($sizes[$size] ?? $sizes['md'])]) }}>
    <x-atoms.icon name="graduation-cap" :class="$icon[$size] ?? $icon['md']" />
</span>
