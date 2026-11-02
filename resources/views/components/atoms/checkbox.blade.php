@props(['checked' => false])

{{-- Atom: kotak centang (Komponen · Kotak centang). Area sentuh 48 px. --}}
<label class="inline-flex min-h-12 cursor-pointer items-center gap-3 text-base text-slate-900">
    <input type="checkbox" @checked($checked) {{ $attributes->merge(['class' => 'peer sr-only']) }}>
    <span class="grid size-6 shrink-0 place-items-center rounded-md border-[1.5px] border-slate-400 bg-white text-transparent transition peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-blue-600">
        <x-atoms.icon name="check" class="size-4 stroke-[3]" />
    </span>
    <span>{{ $slot }}</span>
</label>
