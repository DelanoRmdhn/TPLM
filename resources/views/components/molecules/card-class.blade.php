@props(['title', 'subtitle', 'meta' => null, 'metaIcon' => 'clipboard-check', 'tone' => 'blue', 'href' => '#'])

{{-- Molekul: kartu kelas (Komponen · Kartu kelas) --}}
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 hover:border-slate-300']) }}>
    <x-atoms.icon-tile icon="book-open" :tone="$tone" />

    <div class="min-w-0 flex-1">
        <p class="truncate text-base font-bold text-slate-900">{{ $title }}</p>
        <p class="text-sm text-slate-600">{{ $subtitle }}</p>
        @if ($meta)
            <p class="mt-1 flex items-center gap-1.5 text-sm font-semibold text-amber-700">
                <x-atoms.icon :name="$metaIcon" class="size-4" />
                {{ $meta }}
            </p>
        @endif
    </div>

    <x-atoms.icon name="chevron-right" class="size-5 text-slate-500" />
</a>
