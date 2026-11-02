@props(['label', 'name', 'placeholder' => '', 'hint' => null, 'error' => null, 'toggle' => true])

{{-- Molekul: kolom kata sandi + kotak "Tampilkan kata sandi" (perilaku di resources/js/app.js) --}}
<div class="space-y-1">
    <x-molecules.form-field
        :label="$label"
        :name="$name"
        type="password"
        :placeholder="$placeholder"
        :hint="$hint"
        :error="$error"
        autocomplete="off"
        {{ $attributes }}
    />

    @if ($toggle)
        <x-atoms.checkbox :data-toggle-password="'#'.$name">Tampilkan kata sandi</x-atoms.checkbox>
    @endif
</div>
