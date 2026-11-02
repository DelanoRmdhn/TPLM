@props([
    'name',
    'initials',
    'subtitle',
    'status' => 'Siswa · Aktif',
    'statusVariant' => 'graded',
    'active' => true,
])

{{-- Molekul: kartu akun (Komponen · Kartu akun). Tindakan: reset sandi + nonaktifkan / aktifkan. --}}
<x-molecules.card {{ $attributes }}>
    <div class="flex items-center gap-3">
        <x-atoms.avatar :initials="$initials" />
        <div class="min-w-0">
            <p class="truncate text-base font-bold text-slate-900">{{ $name }}</p>
            <p class="text-sm text-slate-600">{{ $subtitle }}</p>
        </div>
    </div>

    <x-atoms.badge :variant="$statusVariant" class="mt-3">{{ $status }}</x-atoms.badge>

    <div class="mt-3 grid grid-cols-2 gap-2">
        <x-atoms.button variant="neutral-outline" icon="key">Reset Sandi</x-atoms.button>

        @if ($active)
            <x-atoms.button variant="danger">Nonaktifkan</x-atoms.button>
        @else
            <x-atoms.button variant="outline" icon="user-check">Aktifkan</x-atoms.button>
        @endif
    </div>
</x-molecules.card>
