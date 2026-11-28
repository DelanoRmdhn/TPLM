{{-- Pilihan (dropdown) dengan label, petunjuk, dan error. Isi <option> lewat slot. --}}
@props(['name', 'label', 'hint' => null, 'id' => null])

@php($id ??= $name)

<div>
    <label for="{{ $id }}" class="mb-1.5 block font-bold">{{ $label }}</label>
    <div class="relative">
        <select id="{{ $id }}" name="{{ $name }}"
            @if ($hint) aria-describedby="{{ $id }}-hint" @endif
            @error($name) aria-invalid="true" @enderror
            {{ $attributes->class([
                'h-13 w-full appearance-none rounded-control border-2 bg-surface pr-11 pl-4 text-body transition outline-none',
                'focus:border-brand focus:ring-4 focus:ring-brand-100',
                'border-danger' => $errors->has($name),
                'border-border' => ! $errors->has($name),
            ]) }}>
            {{ $slot }}
        </select>
        <x-icon name="caret-down" variant="bold" class="pointer-events-none absolute top-1/2 right-4 size-5 -translate-y-1/2 text-text-muted" />
    </div>
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
