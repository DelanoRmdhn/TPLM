@props(['icon' => 'file-text', 'tone' => 'blue', 'title', 'subtitle' => null, 'href' => null, 'chevron' => false, 'muted' => false])

{{-- Molekul: baris daftar (materi, tugas di kelas, file lampiran) --}}
@php($tag = $href ? 'a' : 'div')

<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4'.($muted ? ' opacity-60' : '')]) }}>
    <x-atoms.icon-tile :icon="$icon" :tone="$tone" />

    <div class="min-w-0 flex-1">
        <p class="text-base font-bold text-slate-900">{{ $title }}</p>
        @if ($subtitle)<p class="mt-0.5 text-sm text-slate-600">{{ $subtitle }}</p>@endif
        {{ $slot }}
    </div>

    @if ($chevron)<x-atoms.icon name="chevron-right" class="mt-3 size-5 text-slate-500" />@endif
</{{ $tag }}>
