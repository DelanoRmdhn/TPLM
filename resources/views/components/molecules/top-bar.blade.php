@props(['title', 'subtitle' => null, 'back' => null, 'badge' => null])

{{-- Molekul: bilah atas (Komponen · Bilah atas). Tombol "Kembali" selalu disertai teks. --}}
<header class="space-y-3 border-b border-slate-200 bg-white px-4 pb-4 pt-4">
    @if ($back)
        <a
            href="{{ $back === true ? 'javascript:history.back()' : $back }}"
            class="inline-flex h-11 items-center gap-2 rounded-[10px] bg-slate-100 px-3 text-base font-semibold text-slate-800 hover:bg-slate-200"
        >
            <x-atoms.icon name="arrow-left" class="size-5" />
            Kembali
        </a>
    @endif

    <div>
        <h1 class="flex items-center gap-2 text-h1 font-bold text-slate-900">
            {{ $title }}
            @if ($badge)<span class="rounded-full bg-red-700 px-2.5 py-0.5 text-sm font-semibold text-white">{{ $badge }}</span>@endif
        </h1>
        @if ($subtitle)<p class="mt-1 text-sm text-slate-600">{{ $subtitle }}</p>@endif
    </div>

    {{ $slot }}
</header>
