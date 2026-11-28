{{-- Kartu putih membulat dengan bayangan lembut. as: section | div | article | a --}}
@props(['as' => 'section', 'padding' => 'p-5'])

<{{ $as }} {{ $attributes->class(['block rounded-card bg-surface shadow-card', $padding]) }}>
    {{ $slot }}
</{{ $as }}>
