@props(['icon', 'tone' => 'slate'])

{{-- Molekul: baris keterangan beribu ikon (tenggat, nilai maksimal, dikumpulkan) --}}
<p {{ $attributes->merge(['class' => 'flex items-start gap-2.5 text-base leading-6 text-slate-900']) }}>
    <x-atoms.icon :name="$icon" class="mt-0.5 size-5 {{ $tone === 'green' ? 'text-green-700' : 'text-slate-600' }}" />
    <span>{{ $slot }}</span>
</p>
