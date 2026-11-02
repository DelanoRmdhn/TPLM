@props(['label', 'title' => null])

{{-- Molekul: penanda bab di kelas ("BAB 2" + "Pecahan Senilai") --}}
<div {{ $attributes->merge(['class' => 'pt-2']) }}>
    <p class="text-sm font-semibold tracking-wide text-blue-700">{{ $label }}</p>
    @if ($title)<h2 class="text-xl font-bold text-slate-900">{{ $title }}</h2>@endif
</div>
