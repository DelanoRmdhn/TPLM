{{-- Kotak ikon berwarna per mapel, supaya siswa mengenali kelas dari warna & gambarnya. size: md | lg --}}
@props(['subject', 'size' => 'md'])

@php
    // Daftar kelas lengkap (bukan disusun dinamis) supaya terbaca Tailwind
    $colors = [
        'blue' => 'bg-mapel-blue-soft text-mapel-blue',
        'green' => 'bg-mapel-green-soft text-mapel-green',
        'orange' => 'bg-mapel-orange-soft text-mapel-orange',
        'purple' => 'bg-mapel-purple-soft text-mapel-purple',
        'pink' => 'bg-mapel-pink-soft text-mapel-pink',
        'teal' => 'bg-mapel-teal-soft text-mapel-teal',
        'red' => 'bg-mapel-red-soft text-mapel-red',
        'amber' => 'bg-mapel-amber-soft text-mapel-amber',
    ];
@endphp

<span {{ $attributes->class([
    'inline-flex shrink-0 items-center justify-center',
    'size-12 rounded-[16px]' => $size === 'md',
    'size-16 rounded-[20px]' => $size === 'lg',
    $colors[$subject->colorKey()],
]) }}>
    <x-icon :name="$subject->iconName()" :class="$size === 'lg' ? 'size-9' : 'size-7'" />
</span>
