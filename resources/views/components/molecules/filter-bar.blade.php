@props(['items', 'active'])

{{-- Molekul: baris chip filter (Tugas Saya, Pelajaran, Nilai, Akun) --}}
<div {{ $attributes->merge(['class' => 'flex gap-2 overflow-x-auto pb-1']) }}>
    @foreach ($items as $key => $label)
        <x-atoms.chip :active="$key === $active">{{ $label }}</x-atoms.chip>
    @endforeach
</div>
