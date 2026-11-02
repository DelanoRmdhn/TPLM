@props(['title', 'subtitle', 'status' => 'pending', 'statusLabel' => 'Belum dikerjakan', 'icon' => 'clipboard-check', 'href' => '#'])

{{-- Molekul: item tugas (Komponen · Item tugas). Kotak ikon mengikuti status. --}}
@php
    $tones = ['pending' => 'amber', 'late' => 'red', 'submitted' => 'blue', 'graded' => 'green'];
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 hover:border-slate-300']) }}>
    <x-atoms.icon-tile :icon="$icon" :tone="$tones[$status] ?? 'amber'" />

    <div class="min-w-0 flex-1">
        <p class="text-base font-bold text-slate-900">{{ $title }}</p>
        <p class="mt-0.5 text-sm text-slate-600">{{ $subtitle }}</p>
        @if ($statusLabel)
            <x-atoms.badge :variant="$status" class="mt-2">{{ $statusLabel }}</x-atoms.badge>
        @endif
    </div>
</a>
