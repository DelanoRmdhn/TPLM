{{--
    Ikon Phosphor. Contoh: <x-icon name="camera" />, <x-icon name="house" variant="fill" class="size-7" />
    variant: regular | bold | fill | duotone (bawaan duotone, terasa lebih hangat untuk anak).
    Ukuran bawaan size-6, kecuali diberi kelas size-* sendiri. Dekoratif (aria-hidden) kecuali diberi label.
--}}
@props(['name', 'variant' => 'duotone', 'label' => null])

@php
    $hasSize = preg_match('/(^|\s)size-/', (string) $attributes->get('class'));
    $attributes = $attributes->class(['shrink-0', 'size-6' => ! $hasSize])
        ->merge($label ? ['role' => 'img', 'aria-label' => $label] : ['aria-hidden' => 'true']);
@endphp

{{ svg('phosphor-'.$name.($variant === 'regular' ? '' : '-'.$variant), $attributes->get('class'), $attributes->except('class')->getAttributes()) }}
