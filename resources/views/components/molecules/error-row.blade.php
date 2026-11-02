@props(['row', 'message'])

{{-- Molekul: baris yang perlu diperbaiki (Hasil Impor Excel) --}}
<div {{ $attributes->merge(['class' => 'rounded-[14px] border border-red-600 bg-white p-3']) }}>
    <p class="flex items-center gap-1.5 text-base font-bold text-red-700">
        <x-atoms.icon name="alert-circle" class="size-5" />
        {{ $row }}
    </p>
    <p class="mt-1 text-base leading-6 text-slate-800">{{ $message }}</p>
</div>
