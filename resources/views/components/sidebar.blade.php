{{--
    Sidebar navigasi untuk layar lebar (lg ke atas): logo sekolah, menu utama, kartu pengguna + keluar.
    Di HP & tablet memakai navigasi bawah (components/bottom-nav).
--}}
@props(['unread' => 0])

@php
    $user = auth()->user();
    $items = \App\Support\Navigation::for($user, request(), $unread);
@endphp

<aside class="fixed inset-y-0 left-0 z-40 hidden w-72 flex-col border-r border-border bg-surface px-5 py-6 lg:flex">
    <a href="{{ route($user->homeRoute()) }}" class="flex items-center gap-3 px-2">
        <img src="{{ asset('icons/logo-sdn.png') }}" alt="" width="48" height="48" class="size-12 shrink-0 object-contain">
        <span class="leading-tight">
            <span class="block text-[17px] font-extrabold">SDN 002</span>
            <span class="block text-caption font-bold text-text-muted">Karangmulya</span>
        </span>
    </a>

    <nav aria-label="Navigasi utama" class="mt-8 flex-1">
        <p class="mb-2 px-3 text-[12px] font-extrabold tracking-wider text-text-muted uppercase">Menu</p>
        <ul class="space-y-1">
            @foreach ($items as $item)
                <li>
                    <a href="{{ route($item['route']) }}" @if ($item['active']) aria-current="page" @endif
                        @class([
                            'flex min-h-12 items-center gap-3 rounded-[16px] px-3 font-bold transition',
                            'bg-brand text-white shadow-button' => $item['active'],
                            'text-text-muted hover:bg-brand-50 hover:text-brand' => ! $item['active'],
                        ])>
                        <x-icon :name="$item['icon']" :variant="$item['active'] ? 'fill' : 'duotone'" class="size-6" />
                        <span class="flex-1">{{ $item['label'] }}</span>
                        @if ($item['badge'] > 0)
                            <span @class([
                                'flex h-6 min-w-6 items-center justify-center rounded-full px-1.5 text-[12px] font-extrabold',
                                'bg-white text-brand' => $item['active'],
                                'bg-danger text-white' => ! $item['active'],
                            ])>
                                {{ $item['badge'] > 9 ? '9+' : $item['badge'] }}<span class="sr-only"> belum dibaca</span>
                            </span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    {{-- Kartu pengguna --}}
    <div class="flex items-center gap-3 rounded-[18px] bg-background p-3">
        <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-brand text-h2 font-extrabold text-white">
            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
        </span>
        <span class="min-w-0 flex-1">
            <span class="block truncate font-extrabold">{{ $user->name }}</span>
            <span class="block truncate text-caption text-text-muted">
                {{ $user->role->label() }}{{ $user->rombel ? ', Kelas '.$user->rombel->name : '' }}
            </span>
        </span>
        <form method="POST" action="{{ route('logout') }}" data-confirm-title="Keluar dari akun?" data-confirm="Kamu perlu masuk lagi dengan NISN/username dan kata sandi." data-confirm-label="Ya, keluar">
            @csrf
            <button type="submit" aria-label="Keluar" class="flex size-10 items-center justify-center rounded-full text-text-muted hover:bg-danger-50 hover:text-danger">
                <x-icon name="sign-out" class="size-5" />
            </button>
        </form>
    </div>
</aside>
