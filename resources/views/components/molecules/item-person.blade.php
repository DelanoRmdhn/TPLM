@props(['name', 'initials', 'subtitle', 'badge' => null, 'badgeVariant' => 'neutral', 'flat' => false])

{{-- Molekul: item orang (Komponen · Item orang). "flat" = tanpa bingkai, dipakai di dalam kartu lain. --}}
<div {{ $attributes->merge(['class' => 'flex items-center gap-3 '.($flat ? '' : 'rounded-2xl border border-slate-200 bg-white p-4')]) }}>
    <x-atoms.avatar :initials="$initials" />

    <div class="min-w-0 flex-1">
        <p class="truncate text-base font-bold text-slate-900">{{ $name }}</p>
        <p class="text-sm text-slate-600">{{ $subtitle }}</p>
        @if ($badge)
            <x-atoms.badge :variant="$badgeVariant" class="mt-1.5">{{ $badge }}</x-atoms.badge>
        @endif
    </div>

    {{ $slot }}
</div>
