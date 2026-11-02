@props(['title', 'description', 'checked' => false, 'name' => 'pilihan'])

{{-- Molekul: pilihan kartu (Terbitkan sekarang / Jadwalkan) --}}
<label class="group flex cursor-pointer items-start gap-3 rounded-[14px] border-[1.5px] border-slate-300 bg-white p-4 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-100">
    <input type="radio" name="{{ $name }}" @checked($checked) class="sr-only">
    <span class="mt-0.5 grid size-6 shrink-0 place-items-center rounded-full border-[1.5px] border-slate-400 bg-white group-has-[:checked]:border-blue-600">
        <span class="size-3 rounded-full bg-transparent group-has-[:checked]:bg-blue-600"></span>
    </span>
    <span>
        <span class="block text-base font-semibold text-slate-900">{{ $title }}</span>
        <span class="block text-sm text-slate-600">{{ $description }}</span>
    </span>
</label>
