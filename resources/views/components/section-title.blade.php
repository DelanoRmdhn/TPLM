{{-- Judul bagian halaman + aksi opsional di kanan (mis. "Lihat semua"). --}}
@props(['icon' => null, 'href' => null, 'action' => null])

<div {{ $attributes->class(['flex items-center justify-between gap-3']) }}>
    <h2 class="flex items-center gap-2 text-h2 font-extrabold">
        @if ($icon) <x-icon :name="$icon" class="size-6 text-brand" /> @endif
        {{ $slot }}
    </h2>
    @if ($href && $action)
        <a href="{{ $href }}" class="-mr-2 flex min-h-11 shrink-0 items-center gap-1 rounded-full px-3 text-caption font-bold text-brand hover:bg-brand-50">
            {{ $action }}
            <x-icon name="caret-right" variant="bold" class="size-4" />
        </a>
    @endif
</div>
