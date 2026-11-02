@props(['title', 'action' => null, 'actionHref' => '#', 'tone' => 'default'])

{{-- Molekul: judul bagian + tautan di kanan (mis. "Belum dikerjakan" · "Lihat Semua") --}}
<div {{ $attributes->merge(['class' => 'flex items-center justify-between gap-3']) }}>
    <h2 class="text-base font-bold {{ $tone === 'danger' ? 'text-red-700' : 'text-slate-900' }}">{{ $title }}</h2>

    @if ($action)
        <a href="{{ $actionHref }}" class="inline-flex h-11 items-center rounded-[10px] bg-slate-100 px-4 text-base font-semibold text-slate-800 hover:bg-slate-200">{{ $action }}</a>
    @endif
</div>
