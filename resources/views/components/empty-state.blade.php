{{--
    Keadaan kosong yang ramah, mis. "Belum ada tugas". Slot = penjelasan, slot actions = tombol.
    <x-empty-state icon="confetti" title="Semua tugas beres!">Tidak ada tugas yang menunggu.</x-empty-state>
--}}
@props(['icon' => 'smiley', 'title'])

<div {{ $attributes->class(['flex flex-col items-center px-6 py-8 text-center']) }}>
    <span class="flex size-20 items-center justify-center rounded-full bg-brand-100 text-brand motion-safe:animate-pop">
        <x-icon :name="$icon" variant="fill" class="size-10" />
    </span>
    <p class="mt-4 text-h2 font-extrabold">{{ $title }}</p>
    @if ($slot->isNotEmpty())
        <p class="mt-1 max-w-xs text-text-muted">{{ $slot }}</p>
    @endif
    @isset($actions)
        <div class="mt-5 flex w-full max-w-xs flex-col gap-2">{{ $actions }}</div>
    @endisset
</div>
