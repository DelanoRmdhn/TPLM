{{-- Satu toast. tone: success | info | error --}}
@props(['tone' => 'success'])

@php
    $icon = ['success' => 'check-circle', 'info' => 'info', 'error' => 'warning-circle'][$tone] ?? 'check-circle';
    $iconColor = ['success' => 'text-[#4ade80]', 'info' => 'text-brand-200', 'error' => 'text-[#fca5a5]'][$tone] ?? 'text-[#4ade80]';
@endphp

<div role="status" data-toast
    class="pointer-events-auto flex w-full max-w-md items-start gap-3 rounded-[18px] bg-text px-4 py-3 text-white shadow-raised motion-safe:animate-slide-up">
    <x-icon :name="$icon" variant="fill" data-toast-icon class="mt-0.5 size-6 {{ $iconColor }}" />
    <p data-toast-text class="min-w-0 flex-1 font-semibold">{{ $slot }}</p>
    <button type="button" data-toast-close aria-label="Tutup pesan" class="-m-2 flex size-10 shrink-0 items-center justify-center rounded-full text-white/70 hover:bg-white/10">
        <x-icon name="x" variant="bold" class="size-5" />
    </button>
</div>
