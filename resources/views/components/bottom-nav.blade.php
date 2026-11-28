{{--
    Navigasi bawah (HP & tablet). Pil biru yang melayang di atas garis gestur; menu aktif jadi kapsul putih
    berisi ikon + label. Di layar lebar (lg) diganti sidebar (components/sidebar).
--}}
@props(['unread' => 0])

@php($items = \App\Support\Navigation::for(auth()->user(), request(), $unread))

<nav aria-label="Navigasi utama" class="pointer-events-none fixed inset-x-0 bottom-nav-offset z-40 px-safe lg:hidden">
    <ul class="pointer-events-auto mx-auto flex h-[68px] max-w-md items-center justify-between rounded-full bg-brand px-2 shadow-[0_10px_30px_rgb(37_99_235/0.35)]">
        @foreach ($items as $item)
            <li @class(['flex', 'flex-1' => ! $item['active']])>
                <a href="{{ route($item['route']) }}" @if ($item['active']) aria-current="page" @endif
                    @class([
                        'relative flex h-12 items-center justify-center gap-1.5 rounded-full transition',
                        'bg-white px-4 text-brand' => $item['active'],
                        'w-full text-white/75 hover:text-white active:bg-white/15' => ! $item['active'],
                    ])>
                    <x-icon :name="$item['icon']" :variant="$item['active'] ? 'fill' : 'regular'" class="size-6" />
                    {{-- Label selalu ada untuk pembaca layar; tampil hanya di menu aktif supaya tidak sesak --}}
                    <span @class(['text-caption font-extrabold', 'sr-only' => ! $item['active']])>{{ $item['label'] }}</span>
                    @if ($item['badge'] > 0)
                        <span class="absolute top-1 right-[calc(50%-20px)] flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-danger px-1 text-[11px] leading-none font-extrabold text-white ring-2 ring-brand">
                            {{ $item['badge'] > 9 ? '9+' : $item['badge'] }}<span class="sr-only"> belum dibaca</span>
                        </span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</nav>
