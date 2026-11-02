@props(['highlight' => false, 'muted' => false])

{{-- Molekul dasar: kartu putih berbingkai. "highlight" = bingkai biru tebal, "muted" = latar abu-abu. --}}
<div {{ $attributes->merge(['class' => 'rounded-2xl p-4 '.match (true) {
    $highlight => 'border-2 border-blue-600 bg-white',
    $muted => 'border border-slate-200 bg-slate-100',
    default => 'border border-slate-200 bg-white',
}]) }}>{{ $slot }}</div>
