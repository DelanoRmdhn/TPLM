@props(['checked' => false])

{{-- Atom: sakelar (Komponen · Sakelar) --}}
<label class="relative inline-flex h-8 w-14 shrink-0 cursor-pointer">
    <input type="checkbox" role="switch" @checked($checked) {{ $attributes->merge(['class' => 'peer sr-only']) }}>
    <span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-blue-600 peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-blue-600"></span>
    <span class="absolute left-1 top-1 size-6 rounded-full bg-white shadow transition peer-checked:translate-x-6"></span>
</label>
