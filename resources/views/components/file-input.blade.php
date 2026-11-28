{{--
    Pilih file berbentuk kotak besar yang mudah diketuk. Jenis & ukuran dicek di HP (data-file-types)
    sebelum dikirim, lalu nama file yang dipilih tampil di bawahnya.
    <x-file-input name="files[]" label="Lampiran" types="pdf,jpg,jpeg,png,docx" :max-mb="10" multiple />
--}}
@props(['name', 'label', 'types', 'maxMb' => 10, 'hint' => null, 'optional' => false, 'id' => null])

@php
    $id ??= str_replace(['[', ']'], '', $name);
    $extensions = explode(',', $types);
    $accept = implode(',', array_map(fn ($ext) => '.'.$ext, $extensions));
    $labels = collect($extensions)->map(fn ($ext) => match ($ext) { 'jpg', 'jpeg' => 'JPG', 'docx' => 'Word', default => strtoupper($ext) })->unique()->values();
    $typeText = $labels->count() > 1 ? $labels->slice(0, -1)->join(', ').', atau '.$labels->last() : $labels->first();
@endphp

<div>
    <p class="mb-1.5 font-bold">
        {{ $label }}
        @if ($optional) <span class="font-semibold text-text-muted">(boleh kosong)</span> @endif
    </p>
    <label for="{{ $id }}" class="flex min-h-24 cursor-pointer flex-col items-center justify-center gap-1 rounded-[18px] border-2 border-dashed border-brand-200 bg-brand-50 p-4 text-center text-brand transition hover:bg-brand-100 has-focus-visible:outline-3 has-focus-visible:outline-brand-200">
        <x-icon name="upload-simple" class="size-8" />
        <span class="font-extrabold">Pilih file</span>
        <span class="text-caption text-text-muted">{{ $hint ?? $typeText.'. Maksimal '.$maxMb.' MB per file.' }}</span>
        <input id="{{ $id }}" name="{{ $name }}" type="file" accept="{{ $accept }}" data-file-types="{{ $types }}" data-file-max-mb="{{ $maxMb }}"
            data-file-list="{{ $id }}-daftar" {{ $attributes->class(['sr-only']) }}>
    </label>
    <ul id="{{ $id }}-daftar" class="mt-2 space-y-1.5 empty:hidden" aria-live="polite"></ul>
</div>
