@props(['title', 'body', 'time', 'icon' => 'clipboard-check', 'tone' => 'blue', 'unread' => true])

{{-- Molekul: item notifikasi (Komponen · Item notifikasi). Titik biru = belum dibaca. --}}
<div {{ $attributes->merge(['class' => 'relative flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4']) }}>
    <x-atoms.icon-tile :icon="$icon" :tone="$tone" />

    <div class="min-w-0 flex-1 pr-4">
        <p class="text-base font-bold text-slate-900">{{ $title }}</p>
        <p class="text-base leading-6 text-slate-800">{{ $body }}</p>
        <p class="mt-1 text-sm text-slate-600">{{ $time }}</p>
    </div>

    @if ($unread)
        <span class="absolute right-4 top-4 size-2.5 rounded-full bg-blue-600" aria-label="Belum dibaca"></span>
    @endif
</div>
