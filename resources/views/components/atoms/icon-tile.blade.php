@props(['icon', 'tone' => 'blue', 'size' => 'md', 'round' => false])

{{-- Atom: kotak ikon berwarna (dipakai kartu kelas, item tugas, notifikasi, pop-up). "round" = bulat. --}}
@php
    $tones = [
        'blue' => 'bg-blue-100 text-blue-700',
        'green' => 'bg-green-100 text-green-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'red' => 'bg-red-100 text-red-700',
        'slate' => 'bg-slate-100 text-slate-600',
    ];
    $sizes = [
        'sm' => ['size-10', 'rounded-[10px]'],
        'md' => ['size-12', 'rounded-xl'],
        'lg' => ['size-14', 'rounded-[14px]'],
        'xl' => ['size-20', 'rounded-[20px]'],
    ];
    [$box, $corner] = $sizes[$size] ?? $sizes['md'];
@endphp

<span {{ $attributes->merge(['class' => "grid shrink-0 place-items-center $box ".($round ? 'rounded-full' : $corner).' '.($tones[$tone] ?? $tones['blue'])]) }}>
    <x-atoms.icon :name="$icon" :class="$size === 'xl' ? 'size-10' : ''" />
</span>
