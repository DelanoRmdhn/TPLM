@props(['variant' => 'info', 'icon' => null, 'title' => null])

{{-- Molekul: kotak pemberitahuan (info biru, peringatan kuning, berhasil hijau, galat merah, netral abu-abu) --}}
@php
    $variants = [
        'info' => ['bg-blue-100 text-slate-900', 'text-blue-600', 'info'],
        'warning' => ['bg-amber-100 text-slate-900', 'text-amber-700', 'key'],
        'success' => ['border border-green-300 bg-green-100 text-slate-900', 'text-green-700', 'check'],
        'danger' => ['bg-red-100 text-slate-900', 'text-red-700', 'alert-circle'],
        'neutral' => ['bg-slate-100 text-slate-800', 'text-slate-600', 'lock'],
    ];
    [$tone, $iconTone, $defaultIcon] = $variants[$variant] ?? $variants['info'];
@endphp

<div {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-[14px] p-4 text-base leading-6 $tone"]) }}>
    <x-atoms.icon :name="$icon ?? $defaultIcon" class="mt-0.5 {{ $iconTone }}" />
    <div class="min-w-0 flex-1">
        @if ($title)<p class="font-semibold">{{ $title }}</p>@endif
        {{ $slot }}
    </div>
</div>
