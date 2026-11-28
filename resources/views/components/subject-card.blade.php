{{--
    Kartu kelas berwarna penuh sesuai mapel: ikon di kotak transparan, nama kelas besar, nama guru di bawah.
    <x-subject-card :classroom="$kelas" :href="route('kelas.show', $kelas)" badge="2 tugas" />
--}}
@props(['classroom', 'href', 'badge' => null])

@php($subject = $classroom->subject)

<a href="{{ $href }}"
    {{ $attributes->class(['group relative flex min-h-44 flex-col overflow-hidden rounded-[26px] p-4 shadow-card transition active:scale-[0.98]', $subject->solidClasses()]) }}>
    {{-- Hiasan --}}
    <span class="pointer-events-none absolute -right-6 -bottom-8 size-28 rounded-full bg-white/15" aria-hidden="true"></span>
    <span class="pointer-events-none absolute -top-5 right-10 size-12 rounded-full bg-white/10" aria-hidden="true"></span>

    <div class="relative flex items-start justify-between">
        <span class="flex size-12 items-center justify-center rounded-[16px] bg-white/25">
            <x-icon :name="$subject->iconName()" variant="fill" class="size-7" />
        </span>
        <span class="flex size-8 items-center justify-center rounded-full bg-white/25 transition group-hover:bg-white/40">
            <x-icon name="arrow-up-right" variant="bold" class="size-4" />
        </span>
    </div>

    @if ($badge)
        <span class="relative mt-3 inline-flex w-fit items-center gap-1 rounded-full bg-white/90 px-2.5 py-0.5 text-[12px] font-extrabold text-text">
            <x-icon name="hourglass-medium" variant="fill" class="size-3.5 text-warning" />
            {{ $badge }}
        </span>
    @endif

    <div class="relative mt-auto pt-3">
        <h3 class="text-[19px] leading-tight font-extrabold">{{ $classroom->name }}</h3>
        <p class="mt-1.5 flex items-center gap-1.5 text-[12px] font-semibold opacity-90">
            <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-white/30 text-[11px] font-extrabold">
                {{ mb_strtoupper(mb_substr($classroom->teacher->name, 0, 1)) }}
            </span>
            <span class="truncate">{{ $classroom->teacher->name }}</span>
        </p>
    </div>
</a>
