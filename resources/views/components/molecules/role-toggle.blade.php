@props(['active' => 'siswa'])

{{-- Molekul: pilihan "Daftar sebagai" Siswa / Guru (Komponen · Form daftar) --}}
@php
    $options = ['siswa' => ['Siswa', 'graduation-cap'], 'guru' => ['Guru', 'book-open']];
@endphp

<div class="space-y-2">
    <x-atoms.label>Daftar sebagai</x-atoms.label>

    <div class="grid grid-cols-2 gap-2" role="radiogroup" aria-label="Daftar sebagai">
        @foreach ($options as $key => [$label, $icon])
            <button
                type="button"
                role="radio"
                aria-checked="{{ $key === $active ? 'true' : 'false' }}"
                class="inline-flex h-[52px] items-center justify-center gap-2 rounded-[10px] border-[1.5px] text-base font-semibold {{ $key === $active ? 'border-blue-600 bg-blue-100 text-blue-700' : 'border-slate-300 bg-white text-slate-800' }}"
            >
                <x-atoms.icon :name="$icon" />
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>
