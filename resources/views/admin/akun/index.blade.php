{{-- M08 Kelola akun guru & siswa: cari/saring, tambah, ubah, reset kata sandi, aktif/nonaktif, buka/tutup pendaftaran. --}}
@extends('layouts.app')

@section('title', 'Kelola Akun')

@section('content')

    {{-- M08c: kata sandi sementara (tampil sekali) --}}
    @if ($temp = session('temporary_password'))
        <section class="rounded-card bg-sunny-50 p-5 ring-2 ring-sunny/60">
            <p class="flex items-center gap-2 font-extrabold">
                <x-icon name="key" variant="fill" class="size-6 text-warning" />
                {{ $temp['title'] }}
            </p>
            <p class="mt-1">{{ $temp['name'] }} <span class="text-text-muted">({{ $temp['username'] }})</span></p>
            <p class="mt-3 text-caption font-bold text-text-muted">Kata sandi sementara</p>
            <p class="mt-1 rounded-control bg-surface px-4 py-3 text-center font-mono text-h1 font-extrabold tracking-widest">{{ $temp['password'] }}</p>
            <p class="mt-2 text-caption text-[#8a5a00]">Catat dan berikan ke pemilik akun. Kata sandi ini hanya tampil sekali dan wajib diganti saat login.</p>
        </section>
    @endif

    {{-- M08e: buka/tutup pendaftaran --}}
    <x-card padding="p-4" class="flex flex-wrap items-center gap-3">
        <span @class(['flex size-12 shrink-0 items-center justify-center rounded-full', 'bg-success-50 text-success' => $registrationOpen, 'bg-background text-text-muted' => ! $registrationOpen])>
            <x-icon :name="$registrationOpen ? 'lock-key-open' : 'lock-key'" class="size-7" />
        </span>
        <span class="min-w-0 flex-1">
            <span class="block font-extrabold">Pendaftaran akun mandiri</span>
            <span class="block text-caption {{ $registrationOpen ? 'font-bold text-success' : 'text-text-muted' }}">Status: {{ $registrationOpen ? 'Dibuka' : 'Ditutup' }}</span>
        </span>
        <form method="POST" action="{{ route('admin.pendaftaran.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="is_open" value="{{ $registrationOpen ? 0 : 1 }}">
            <x-button :variant="$registrationOpen ? 'danger' : 'primary'" size="sm">{{ $registrationOpen ? 'Tutup Pendaftaran' : 'Buka Pendaftaran' }}</x-button>
        </form>
    </x-card>

    <div class="grid grid-cols-2 gap-3">
        <x-button :href="route('admin.akun.create')" icon="user-plus">Tambah Akun</x-button>
        <x-button :href="route('admin.impor.index')" variant="secondary" icon="microsoft-excel-logo">Impor Excel</x-button>
    </div>

    {{-- Cari & saring --}}
    <form method="GET" class="space-y-2 rounded-card bg-surface p-4 shadow-card">
        <div class="relative">
            <label for="q" class="sr-only">Cari akun</label>
            <x-icon name="magnifying-glass" class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-text-muted" />
            <input id="q" name="q" value="{{ request('q') }}" placeholder="Cari nama, NISN, atau NIP"
                class="h-12 w-full rounded-control border-2 border-border bg-surface pr-4 pl-11 outline-none focus:border-brand focus:ring-4 focus:ring-brand-100">
        </div>
        <div class="grid grid-cols-3 gap-2">
            @foreach ([
                ['peran', 'Peran', ['' => 'Peran', 'siswa' => 'Siswa', 'guru' => 'Guru']],
                ['rombel', 'Rombel', ['' => 'Rombel'] + $rombels->pluck('name', 'id')->all()],
                ['status', 'Status', ['' => 'Status', 'aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']],
            ] as [$field, $label, $options])
                <div class="relative">
                    <label for="saring-{{ $field }}" class="sr-only">{{ $label }}</label>
                    <select id="saring-{{ $field }}" name="{{ $field }}" class="h-12 w-full appearance-none truncate rounded-control border-2 border-border bg-surface pr-8 pl-3 text-caption font-bold outline-none focus:border-brand">
                        @foreach ($options as $value => $text)
                            <option value="{{ $value }}" @selected((string) request($field) === (string) $value)>{{ $text }}</option>
                        @endforeach
                    </select>
                    <x-icon name="caret-down" variant="bold" class="pointer-events-none absolute top-1/2 right-2.5 size-4 -translate-y-1/2 text-text-muted" />
                </div>
            @endforeach
        </div>
        <x-button variant="soft" icon="funnel" block>Terapkan</x-button>
    </form>

    {{-- Daftar akun --}}
    <section class="space-y-3">
        <p class="text-caption font-bold text-text-muted">{{ $users->total() }} akun</p>

        <div class="grid gap-3 lg:grid-cols-2">
        @forelse ($users as $user)
            <article @class(['rounded-card bg-surface p-4 shadow-card', 'opacity-70' => ! $user->is_active])>
                <div class="flex items-start gap-3">
                    <span @class(['flex size-11 shrink-0 items-center justify-center rounded-full font-extrabold', 'bg-brand-100 text-brand' => $user->isSiswa(), 'bg-success-50 text-success' => $user->isGuru()])>
                        <x-icon :name="$user->isSiswa() ? 'student' : 'chalkboard-teacher'" class="size-6" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-extrabold">{{ $user->name }}</p>
                        <p class="mt-0.5 flex flex-wrap gap-x-3 text-caption text-text-muted">
                            <span>{{ $user->role->label() }}</span>
                            <span class="inline-flex items-center gap-1"><x-icon name="identification-card" class="size-4" />{{ $user->username }}</span>
                            @if ($user->rombel)
                                <span class="inline-flex items-center gap-1"><x-icon name="users-three" class="size-4" />{{ $user->rombel->name }}</span>
                            @endif
                        </p>
                    </div>
                    <x-chip :tone="$user->is_active ? 'success' : 'neutral'" :icon="$user->is_active ? 'check-circle' : 'prohibit'">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</x-chip>
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                    <x-button :href="route('admin.akun.edit', $user)" variant="soft" size="sm" icon="pencil-simple">Ubah</x-button>

                    <form method="POST" action="{{ route('admin.akun.reset-password', $user) }}"
                        data-confirm-title="Reset kata sandi?" data-confirm="{{ $user->name.' akan mendapat kata sandi sementara yang wajib diganti saat login.' }}" data-confirm-label="Ya, reset">
                        @csrf
                        <x-button variant="soft" size="sm" icon="key">Reset Sandi</x-button>
                    </form>

                    @if ($user->is_active)
                        {{-- M08d: konfirmasi nonaktifkan --}}
                        <form method="POST" action="{{ route('admin.akun.deactivate', $user) }}"
                            data-confirm-title="Nonaktifkan akun?" data-confirm="{{ $user->name.' tidak bisa login, tapi datanya tetap tersimpan.' }}" data-confirm-label="Ya, nonaktifkan" data-confirm-tone="danger">
                            @csrf
                            <x-button variant="ghost" size="sm" icon="prohibit" class="text-danger hover:bg-danger-50">Nonaktifkan</x-button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.akun.activate', $user) }}">
                            @csrf
                            <x-button variant="ghost" size="sm" icon="check-circle" class="text-success hover:bg-success-50">Aktifkan</x-button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <x-card>
                <x-empty-state icon="magnifying-glass" title="Tidak ada akun">Belum ada akun yang cocok dengan pencarian.</x-empty-state>
            </x-card>
        @endforelse
        </div>

        {{ $users->links() }}
    </section>

@endsection
