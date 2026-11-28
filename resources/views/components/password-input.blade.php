{{-- Kolom kata sandi dengan tombol mata untuk menampilkan/menyembunyikan isinya. --}}
@props(['name', 'label', 'hint' => null, 'id' => null])

@php($id ??= $name)

<div>
    <label for="{{ $id }}" class="mb-1.5 block font-bold">{{ $label }}</label>
    <div class="relative">
        <input id="{{ $id }}" name="{{ $name }}" type="password"
            @if ($hint) aria-describedby="{{ $id }}-hint" @endif
            @error($name) aria-invalid="true" @enderror
            {{ $attributes->class([
                'h-13 w-full rounded-control border-2 bg-surface pr-14 pl-4 text-body transition outline-none',
                'placeholder:text-text-muted/70 focus:border-brand focus:ring-4 focus:ring-brand-100',
                'border-danger' => $errors->has($name),
                'border-border' => ! $errors->has($name),
            ]) }}>
        <button type="button" data-toggle-password="{{ $id }}" aria-label="Tampilkan kata sandi" aria-pressed="false"
            class="group absolute top-1/2 right-1.5 flex size-10 -translate-y-1/2 items-center justify-center rounded-full text-text-muted hover:bg-brand-50">
            <x-icon name="eye" class="size-6 group-aria-pressed:hidden" />
            <x-icon name="eye-slash" class="hidden size-6 group-aria-pressed:block" />
        </button>
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
