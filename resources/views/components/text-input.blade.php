{{--
    Kolom isian dengan label, petunjuk, dan pesan error di bawahnya.
    <x-text-input name="password" type="password" label="Kata sandi baru" hint="Minimal 8 karakter" />
--}}
@props(['name', 'label', 'type' => 'text', 'hint' => null, 'value' => null, 'id' => null])

@php($id ??= $name)

<div>
    <label for="{{ $id }}" class="mb-1.5 block font-bold">{{ $label }}</label>
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
        @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
        @if ($hint) aria-describedby="{{ $id }}-hint" @endif
        @error($name) aria-invalid="true" @enderror
        {{ $attributes->class([
            'h-13 w-full rounded-control border-2 bg-surface px-4 text-body transition outline-none',
            'placeholder:text-text-muted/70 focus:border-brand focus:ring-4 focus:ring-brand-100',
            'border-danger' => $errors->has($name),
            'border-border' => ! $errors->has($name),
        ]) }}>
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
