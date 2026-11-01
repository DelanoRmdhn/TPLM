{{-- M01d Daftar akun — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Daftar Akun')

@section('content')

    @php($role = old('role', 'siswa'))

    <h1 class="text-h1 font-bold">Daftar Akun Baru</h1>

    <form method="POST" action="{{ route('register.store') }}" class="space-y-4 rounded-card border border-border bg-white p-4">
        @csrf

        <fieldset>
            <legend class="mb-2 font-semibold">Saya mendaftar sebagai</legend>
            <div class="flex gap-3">
                <label class="flex h-12 flex-1 items-center gap-2 rounded-control border border-border px-3">
                    <input type="radio" name="role" value="siswa" @checked($role === 'siswa')> Siswa
                </label>
                <label class="flex h-12 flex-1 items-center gap-2 rounded-control border border-border px-3">
                    <input type="radio" name="role" value="guru" @checked($role === 'guru')> Guru
                </label>
            </div>
        </fieldset>

        <div>
            <label for="name" class="mb-1 block font-semibold">Nama lengkap</label>
            <input id="name" name="name" value="{{ old('name') }}" required autocomplete="name"
                class="h-12 w-full rounded-control border border-border px-3">
        </div>

        <div>
            <label for="username" class="mb-1 block font-semibold" data-label-siswa="NISN" data-label-guru="NIP atau username">
                {{ $role === 'guru' ? 'NIP atau username' : 'NISN' }}
            </label>
            <input id="username" name="username" value="{{ old('username') }}" required autocomplete="username"
                class="h-12 w-full rounded-control border border-border px-3">
        </div>

        <div id="rombel-field" @class(['hidden' => $role === 'guru'])>
            <label for="rombel_id" class="mb-1 block font-semibold">Rombel</label>
            <select id="rombel_id" name="rombel_id" class="h-12 w-full rounded-control border border-border px-3">
                <option value="">Pilih rombel</option>
                @foreach ($rombels as $rombel)
                    <option value="{{ $rombel->id }}" @selected(old('rombel_id') == $rombel->id)>{{ $rombel->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="password" class="mb-1 block font-semibold">Kata sandi</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="Minimal 8 karakter"
                class="h-12 w-full rounded-control border border-border px-3">
        </div>

        <div>
            <label for="password_confirmation" class="mb-1 block font-semibold">Ulangi kata sandi</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                class="h-12 w-full rounded-control border border-border px-3">
        </div>

        <button type="submit" class="h-12 w-full rounded-control bg-brand font-semibold text-white">Daftar</button>
    </form>

    <a href="{{ route('login') }}" class="block text-center text-brand underline">Sudah punya akun? Masuk</a>

    <script>
        // Ganti label & tampilkan rombel sesuai peran
        document.querySelectorAll('input[name="role"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                const role = radio.value;
                const label = document.querySelector('label[for="username"]');
                label.textContent = label.dataset['label' + (role === 'guru' ? 'Guru' : 'Siswa')];
                document.getElementById('rombel-field').classList.toggle('hidden', role === 'guru');
            });
        });
    </script>

@endsection
