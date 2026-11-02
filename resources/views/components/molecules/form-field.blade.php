@props([
    'label',
    'name' => null,
    'hint' => null,
    'error' => null,
    'icon' => null,
    'textarea' => false,
    'rows' => 3,
])

{{-- Molekul: label + kolom isian + petunjuk / pesan bantuan --}}
@php($id = $attributes->get('id', $name))

<div class="space-y-2">
    <x-atoms.label :for="$id">{{ $label }}</x-atoms.label>

    @if ($textarea)
        <x-atoms.textarea :id="$id" :name="$name" :rows="$rows" :error="(bool) $error" {{ $attributes->except('id') }}>{{ $slot }}</x-atoms.textarea>
    @else
        <x-atoms.input :id="$id" :name="$name" :icon="$icon" :error="(bool) $error" {{ $attributes->except('id') }} />
    @endif

    @if ($error)
        <p class="flex items-start gap-1.5 text-sm font-medium text-red-700" role="alert">
            <x-atoms.icon name="alert-circle" class="mt-0.5 size-4" />
            <span>{{ $error }}</span>
        </p>
    @elseif ($hint)
        <p class="flex items-start gap-1.5 text-sm text-slate-600">
            <x-atoms.icon name="info" class="mt-0.5 size-4" />
            <span>{{ $hint }}</span>
        </p>
    @endif
</div>
