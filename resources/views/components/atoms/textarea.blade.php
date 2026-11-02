@props(['error' => false])

{{-- Atom: kolom isian banyak baris --}}
@php
    $tone = $error
        ? 'border-red-600 focus:ring-red-600/20'
        : 'border-slate-300 focus:border-blue-600 focus:ring-blue-600/20';
@endphp

<textarea
    {{ $attributes->merge(['rows' => 3, 'class' => "w-full resize-none rounded-[10px] border bg-white px-4 py-3 text-base leading-6 text-slate-900 outline-none placeholder:text-slate-500 focus:ring-2 $tone"]) }}
>{{ $slot }}</textarea>
