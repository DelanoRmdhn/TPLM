@props(['items', 'active'])

{{-- Molekul: tab (mis. Pelajaran / Forum / Anggota) --}}
<div role="tablist" class="grid grid-flow-col auto-cols-fr gap-1 rounded-[14px] border border-slate-200 bg-slate-100 p-1">
    @foreach ($items as $key => $label)
        <a
            href="#{{ $key }}"
            role="tab"
            aria-selected="{{ $key === $active ? 'true' : 'false' }}"
            class="flex h-12 items-center justify-center rounded-[10px] text-base font-semibold {{ $key === $active ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-700' }}"
        >{{ $label }}</a>
    @endforeach
</div>
