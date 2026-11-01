{{-- Lampiran guru pada tugas — SEMENTARA dari BE --}}
@foreach ($attachments as $attachment)
    @if ($attachment->isLink())
        <a href="{{ $attachment->url }}" target="_blank" rel="noopener" class="flex min-h-12 items-center gap-2 rounded-control border border-border px-3">▶ Video YouTube</a>
    @else
        <a href="{{ route('lampiran.show', $attachment) }}" target="_blank" class="flex min-h-12 items-center justify-between gap-2 rounded-control border border-border px-3">
            <span>📎 {{ $attachment->original_name }}</span>
            <span class="text-caption text-text-muted">{{ $attachment->readableSize() }}</span>
        </a>
    @endif
@endforeach
