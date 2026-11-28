{{--
    Tombol. Jadi <a> kalau diberi href, selain itu <button>.
    variant: primary | secondary | soft | ghost | danger | success
    size: md (48px) | lg (56px) | sm (40px, hanya untuk aksi sekunder di dalam daftar)
    icon: nama ikon Phosphor di kiri teks.
--}}
@props(['variant' => 'primary', 'size' => 'md', 'icon' => null, 'href' => null, 'type' => 'submit', 'block' => false])

@php
    $classes = [
        'inline-flex items-center justify-center gap-2 rounded-control font-bold select-none transition',
        'focus-visible:outline-3 focus-visible:outline-offset-2 focus-visible:outline-brand-200',
        'disabled:cursor-not-allowed disabled:opacity-60',
        'w-full' => $block,
        // Ukuran
        'min-h-10 px-3 text-caption' => $size === 'sm',
        'min-h-12 px-5 text-button' => $size === 'md',
        'min-h-14 px-6 text-[17px]' => $size === 'lg',
        // Gaya: tombol utama sedikit "timbul" dan tertekan saat diketuk, terasa seperti tombol mainan
        'bg-brand text-white shadow-button active:translate-y-[3px] active:shadow-none hover:bg-brand-700' => $variant === 'primary',
        'bg-success text-white shadow-[0_3px_0_#15803d] active:translate-y-[3px] active:shadow-none' => $variant === 'success',
        'bg-danger text-white shadow-[0_3px_0_#b91c1c] active:translate-y-[3px] active:shadow-none' => $variant === 'danger',
        'border-2 border-brand-200 bg-surface text-brand hover:bg-brand-50 active:bg-brand-100' => $variant === 'secondary',
        'bg-brand-50 text-brand hover:bg-brand-100 active:bg-brand-200' => $variant === 'soft',
        'text-brand hover:bg-brand-50 active:bg-brand-100' => $variant === 'ghost',
    ];
    $iconSize = $size === 'sm' ? 'size-5' : 'size-6';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($icon) <x-icon :name="$icon" :class="$iconSize" /> @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        @if ($icon) <x-icon :name="$icon" :class="$iconSize" /> @endif
        {{ $slot }}
    </button>
@endif
