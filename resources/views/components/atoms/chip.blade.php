@props(['active' => false, 'href' => null])

{{-- Atom: chip filter (Komponen · Filter). Tinggi 48 px. --}}
@php
    $classes = 'inline-flex h-12 shrink-0 items-center justify-center rounded-full border px-5 text-base font-semibold transition '
        .($active
            ? 'border-blue-600 bg-blue-600 text-white'
            : 'border-slate-300 bg-white text-slate-900 hover:bg-slate-50');
@endphp

@if ($href)
    <a href="{{ $href }}" @if ($active) aria-current="true" @endif {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="button" @if ($active) aria-pressed="true" @endif {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
