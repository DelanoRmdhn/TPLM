@props(['icon' => null, 'error' => false])

{{-- Atom: kolom isian (Komponen · Kolom isian). Tinggi 52 px, huruf 16 px. --}}
@php
    $tone = $error
        ? 'border-red-600 focus:ring-red-600/20'
        : 'border-slate-300 focus:border-blue-600 focus:ring-blue-600/20';
@endphp

<div class="relative">
    <input
        {{ $attributes->merge(['type' => 'text', 'class' => "h-[52px] w-full rounded-[10px] border bg-white px-4 text-base text-slate-900 outline-none placeholder:text-slate-500 focus:ring-2 $tone ".($icon ? 'pr-11' : '')]) }}
    >
    @if ($icon)
        <x-atoms.icon :name="$icon" class="pointer-events-none absolute right-4 top-1/2 size-5 -translate-y-1/2 text-slate-600" />
    @endif
</div>
