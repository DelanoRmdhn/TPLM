{{--
    Bottom sheet (di HP muncul dari bawah, di layar lebar di tengah). Memakai <dialog> bawaan browser,
    jadi fokus keyboard & tombol Esc sudah ditangani browser.
    Buka: <button data-sheet-open="id-sheet">, tutup: elemen [data-sheet-close] di dalamnya.
    auto-open: langsung terbuka saat halaman dimuat (mis. pesan error dari server).
    tone: brand | danger | warning | success (warna lingkaran ikon)
--}}
@props(['id', 'title' => null, 'icon' => null, 'tone' => 'brand', 'autoOpen' => false])

@php
    $tones = [
        'brand' => 'bg-brand-50 text-brand',
        'danger' => 'bg-danger-50 text-danger',
        'warning' => 'bg-sunny-50 text-warning',
        'success' => 'bg-success-50 text-success',
    ];
@endphp

<dialog id="{{ $id }}" data-sheet @if ($autoOpen) data-auto-open open @endif aria-labelledby="{{ $id }}-judul"
    {{ $attributes->class([
        'mb-0 mt-auto w-full max-w-lg max-h-[88dvh] overflow-y-auto rounded-t-sheet bg-surface p-0 text-text shadow-raised',
        'sm:my-auto sm:rounded-sheet',
    ]) }}>
    <div class="px-6 pt-3 pb-[max(24px,env(safe-area-inset-bottom))]">
        {{-- Pegangan sheet (penanda bisa ditutup) --}}
        <div class="mx-auto mb-4 h-1.5 w-12 rounded-full bg-border sm:hidden" aria-hidden="true"></div>

        @if ($icon)
            <span class="mx-auto flex size-16 items-center justify-center rounded-full {{ $tones[$tone] ?? $tones['brand'] }}">
                <x-icon :name="$icon" class="size-9" />
            </span>
        @endif

        @if ($title)
            <h2 id="{{ $id }}-judul" data-sheet-title class="mt-3 text-center text-h2 font-extrabold">{{ $title }}</h2>
        @endif

        <div class="mt-2 text-center text-text-muted">{{ $slot }}</div>

        @isset($actions)
            <div class="mt-6 flex flex-col gap-2">{{ $actions }}</div>
        @endisset
    </div>
</dialog>
