@props(['open' => true])

{{-- Molekul: kartu pendaftaran (admin) — buka / tutup pendaftaran akun mandiri --}}
<x-molecules.card {{ $attributes }}>
    <div class="flex items-start gap-3">
        <x-atoms.icon-tile icon="user" tone="blue" size="sm" />
        <div>
            <h2 class="text-lg font-bold leading-tight text-slate-900">Pendaftaran Akun</h2>
            <p class="text-sm leading-5 text-slate-600">Atur apakah siswa dan guru boleh daftar sendiri.</p>
        </div>
    </div>

    <x-atoms.badge :variant="$open ? 'graded' : 'neutral'" class="mt-3">{{ $open ? 'Dibuka' : 'Ditutup' }}</x-atoms.badge>

    <p class="mt-3 text-base leading-6 text-slate-800">
        @if ($open)
            Tombol Daftar tampil di halaman login. Siswa dan guru bisa membuat akun sendiri.
        @else
            Tombol Daftar disembunyikan. Akun hanya bisa dibuat oleh admin.
        @endif
    </p>

    <div class="mt-4 flex items-center justify-between gap-3">
        <span class="text-base text-slate-900">Buka pendaftaran</span>
        <x-atoms.switch :checked="$open" aria-label="Buka pendaftaran" />
    </div>

    <x-atoms.button variant="neutral" icon="eye" class="mt-3">Lihat Halaman Login</x-atoms.button>
</x-molecules.card>
