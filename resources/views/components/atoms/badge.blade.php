@props(['variant' => 'neutral', 'dot' => true])

{{-- Atom: status (Komponen · Status). Warna + titik + teks, tidak hanya warna. --}}
@php
    $variants = [
        'pending' => ['bg-amber-100 text-amber-800', 'bg-amber-500'],
        'submitted' => ['bg-blue-100 text-blue-800', 'bg-blue-600'],
        'late' => ['bg-red-100 text-red-700', 'bg-red-600'],
        'graded' => ['bg-green-100 text-green-700', 'bg-green-600'],
        'info' => ['bg-blue-100 text-blue-800', 'bg-blue-600'],
        'neutral' => ['bg-slate-100 text-slate-700', 'bg-slate-500'],
    ];
    [$tone, $dotTone] = $variants[$variant] ?? $variants['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold $tone"]) }}>
    @if ($dot)<span class="size-2 rounded-full {{ $dotTone }}"></span>@endif
    {{ $slot }}
</span>
