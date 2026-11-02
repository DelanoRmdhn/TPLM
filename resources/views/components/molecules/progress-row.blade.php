@props(['title', 'percent', 'detail'])

{{-- Molekul: baris pengumpulan per kelas (Ringkasan Aktivitas). Warna + angka, bukan warna saja. --}}
@php
    $tone = $percent >= 85 ? ['text-green-700', 'bg-green-700'] : ['text-amber-700', 'bg-amber-600'];
@endphp

<x-molecules.card {{ $attributes }}>
    <div class="flex items-center justify-between gap-3">
        <p class="text-base font-bold text-slate-900">{{ $title }}</p>
        <p class="text-base font-bold {{ $tone[0] }}">{{ $percent }}%</p>
    </div>
    <p class="mt-1 text-sm text-slate-600">{{ $detail }}</p>
    <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-slate-200" role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
        <div class="h-full rounded-full {{ $tone[1] }}" style="width: {{ $percent }}%"></div>
    </div>
</x-molecules.card>
