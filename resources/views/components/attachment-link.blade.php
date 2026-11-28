{{-- Satu lampiran guru (file atau video YouTube) sebagai baris yang bisa diketuk. --}}
@props(['attachment', 'bare' => false])

@php
    [$icon, $tone] = match (true) {
        $attachment->isLink() => ['youtube-logo', 'bg-mapel-red-soft text-mapel-red'],
        $attachment->isPdf() => ['file-pdf', 'bg-danger-50 text-danger'],
        $attachment->isImage() => ['image', 'bg-mapel-teal-soft text-mapel-teal'],
        default => ['file-doc', 'bg-brand-50 text-brand'],
    };
@endphp

<a href="{{ $attachment->isLink() ? $attachment->url : route('lampiran.show', $attachment) }}" target="_blank"
    @if ($attachment->isLink()) rel="noopener" @endif
    {{ $attributes->class([
        'flex min-h-14 items-center gap-3 rounded-control transition',
        'border-2 border-border bg-surface p-2 pr-3 hover:border-brand-200 active:bg-brand-50' => ! $bare,
    ]) }}>
    <span class="flex size-11 shrink-0 items-center justify-center rounded-[12px] {{ $tone }}">
        <x-icon :name="$icon" class="size-6" />
    </span>
    <span class="min-w-0 flex-1">
        <span class="block truncate font-bold">{{ $attachment->isLink() ? 'Video YouTube' : $attachment->original_name }}</span>
        @unless ($attachment->isLink())
            <span class="block text-caption text-text-muted">{{ $attachment->readableSize() }}</span>
        @endunless
    </span>
    <x-icon name="arrow-square-out" class="size-5 text-text-muted" />
</a>
