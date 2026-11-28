{{-- M01d Daftar akun mandiri (FR-29, PB-30). Hanya bisa dibuka saat admin membuka pendaftaran. --}}
@extends('layouts.guest')

@section('title', 'Daftar Akun')
@section('heading', 'Daftar Akun Baru')
@section('subheading', 'Isi data dirimu dengan benar ya.')

@section('content')

    @php($role = old('role', 'siswa'))

    <form method="POST" action="{{ route('register.store') }}" class="space-y-4 rounded-card bg-surface p-5 shadow-card">
        @csrf

        <fieldset>
            <legend class="mb-1.5 font-bold">Saya mendaftar sebagai</legend>
            <div class="grid grid-cols-2 gap-3">
                @foreach (['siswa' => ['Siswa', 'student'], 'guru' => ['Guru', 'chalkboard-teacher']] as $value => [$label, $icon])
                    <label class="flex min-h-24 cursor-pointer flex-col items-center justify-center gap-1.5 rounded-[18px] border-2 border-border bg-surface font-extrabold text-text-muted transition has-checked:border-brand has-checked:bg-brand-50 has-checked:text-brand has-focus-visible:outline-3 has-focus-visible:outline-brand-200">
                        <input type="radio" name="role" value="{{ $value }}" class="sr-only" @checked($role === $value)>
                        <x-icon :name="$icon" class="size-9" />
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </fieldset>

        <x-text-input name="name" label="Nama lengkap" required autocomplete="name" />

        <div>
            <x-text-input name="username" :label="$role === 'guru' ? 'NIP atau username' : 'NISN'" required autocomplete="username"
                data-label-siswa="NISN" data-label-guru="NIP atau username" />
        </div>

        <div id="rombel-field" @class(['hidden' => $role === 'guru'])>
            <x-select name="rombel_id" label="Rombel">
                <option value="">Pilih rombel</option>
                @foreach ($rombels as $rombel)
                    <option value="{{ $rombel->id }}" @selected(old('rombel_id') == $rombel->id)>{{ $rombel->name }}</option>
                @endforeach
            </x-select>
        </div>

        <x-password-input name="password" label="Kata sandi" hint="Minimal 8 karakter." required autocomplete="new-password" />
        <x-password-input name="password_confirmation" label="Ulangi kata sandi" required autocomplete="new-password" />

        <x-button icon="user-plus" size="lg" block>Daftar</x-button>
    </form>

    <x-button :href="route('login')" variant="ghost" icon="sign-in" block class="mt-3">Sudah punya akun? Masuk</x-button>

    <script>
        // Ganti label & tampilkan rombel sesuai peran
        document.querySelectorAll('input[name="role"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                const guru = radio.value === 'guru';
                const input = document.getElementById('username');
                document.querySelector('label[for="username"]').textContent = guru ? input.dataset.labelGuru : input.dataset.labelSiswa;
                document.getElementById('rombel-field').classList.toggle('hidden', guru);
            });
        });
    </script>

@endsection
