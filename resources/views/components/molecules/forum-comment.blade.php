@props(['initials', 'name', 'time', 'text'])

{{-- Molekul: satu komentar di forum --}}
<div {{ $attributes->merge(['class' => 'flex items-start gap-2.5']) }}>
    <x-atoms.avatar :initials="$initials" size="xs" />
    <div class="min-w-0 flex-1 rounded-xl bg-slate-100 px-3 py-2">
        <p class="text-sm"><span class="font-bold text-slate-900">{{ $name }}</span> <span class="text-slate-600">· {{ $time }}</span></p>
        <p class="text-base leading-6 text-slate-800">{{ $text }}</p>
    </div>
</div>
