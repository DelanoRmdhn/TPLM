@props(['title', 'subtitle'])

{{-- Molekul: kepala Beranda (logo + sapaan) --}}
<header class="flex items-center gap-3 border-b border-slate-200 bg-white px-4 py-4">
    <x-atoms.logo />
    <div class="min-w-0">
        <h1 class="text-h1 font-bold leading-tight text-slate-900">{{ $title }}</h1>
        <p class="mt-0.5 text-sm text-slate-600">{{ $subtitle }}</p>
    </div>
</header>
