{{-- M08 Tambah/Ubah akun guru atau siswa. Kata sandi awal dibuat otomatis. --}}
@extends('layouts.app')

@section('title', $user->exists ? 'Ubah Akun' : 'Tambah Akun')
@section('back', route('admin.akun.index'))
@section('back-label', 'Kembali ke Kelola Akun')

@section('content')

    @php($role = old('role', $user->role?->value ?? 'siswa'))
    @php($selectedRombel = old('rombel', $user->rombel?->name))

    <form method="POST" action="{{ $user->exists ? route('admin.akun.update', $user) : route('admin.akun.store') }}"
        class="space-y-4 rounded-card bg-surface p-5 shadow-card">
        @csrf
        @if ($user->exists)
            @method('PUT')
        @endif

        <fieldset>
            <legend class="mb-1.5 font-bold">Peran</legend>
            <div class="grid grid-cols-2 gap-3">
                @foreach (['siswa' => ['Siswa', 'student'], 'guru' => ['Guru', 'chalkboard-teacher']] as $value => [$label, $icon])
                    <label class="flex min-h-20 cursor-pointer flex-col items-center justify-center gap-1 rounded-[18px] border-2 border-border font-extrabold text-text-muted transition has-checked:border-brand has-checked:bg-brand-50 has-checked:text-brand has-focus-visible:outline-3 has-focus-visible:outline-brand-200">
                        <input type="radio" name="role" value="{{ $value }}" class="sr-only" @checked($role === $value)>
                        <x-icon :name="$icon" class="size-8" />
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </fieldset>

        <x-text-input name="name" label="Nama" :value="$user->name" required />
        <x-text-input name="username" label="NISN (siswa) atau NIP/username (guru)" :value="$user->username" required />

        <x-select name="rombel" label="Rombel (khusus siswa)" hint="Rombel belum ada? Tambahkan dulu di menu Rombel.">
            <option value="">Pilih rombel</option>
            @foreach ($rombels as $rombel)
                <option value="{{ $rombel->name }}" @selected($selectedRombel === $rombel->name)>{{ $rombel->name }}</option>
            @endforeach
        </x-select>

        @unless ($user->exists)
            <p class="flex items-start gap-2 rounded-control bg-brand-50 p-3 text-caption text-brand-800">
                <x-icon name="key" class="mt-0.5 size-5" />
                Kata sandi awal dibuat otomatis dan ditampilkan setelah disimpan.
            </p>
        @endunless

        <div class="grid grid-cols-2 gap-3">
            <x-button :href="route('admin.akun.index')" variant="secondary">Batal</x-button>
            <x-button icon="floppy-disk">Simpan</x-button>
        </div>
    </form>

@endsection
