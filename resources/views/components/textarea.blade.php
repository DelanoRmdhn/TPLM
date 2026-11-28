{{-- Kolom isian panjang dengan label, petunjuk, dan error. optional: tambahkan "(boleh kosong)" di label. --}}
@props(['name', 'label', 'value' => null, 'hint' => null, 'optional' => false, 'rows' => 3, 'id' => null])

@php($id ??= $name)

<div>
    <label for="{{ $id }}" class="mb-1.5 block font-bold">
        {{ $label }}
        @if ($optional) <span class="font-semibold text-text-muted">(boleh kosong)</span> @endif
    </label>
    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}"
        @if ($hint) aria-describedby="{{ $id }}-hint" @endif
        @error($name) aria-invalid="true" @enderror
        {{ $attributes->class([
            'w-full rounded-control border-2 bg-surface p-4 text-body transition outline-none',
            'placeholder:text-text-muted/70 focus:border-brand focus:ring-4 focus:ring-brand-100',
            'border-danger' => $errors->has($name),
            'border-border' => ! $errors->has($name),
        ]) }}>{{ old($name, $value) }}</textarea>
    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-1.5 text-caption text-text-muted">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1.5 flex items-center gap-1.5 text-caption font-bold text-danger">
            <x-icon name="warning-circle" variant="fill" class="size-4" />
            {{ $message }}
        </p>
    @enderror
</div>
