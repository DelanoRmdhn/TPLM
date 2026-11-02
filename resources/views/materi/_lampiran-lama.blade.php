{{-- Lampiran yang sudah ada di form ubah materi/tugas — SEMENTARA dari BE --}}
@if ($attachments->isNotEmpty())
    <fieldset class="min-w-0">
        <legend class="mb-1 font-semibold">Lampiran sekarang</legend>
        <p class="mb-2 text-caption text-text-muted">Centang yang mau dihapus.</p>
        <div class="space-y-2">
            @foreach ($attachments as $attachment)
                <label class="flex min-h-12 items-center gap-3 rounded-control border border-border px-3 py-2">
                    <input type="checkbox" name="remove_attachments[]" value="{{ $attachment->id }}" class="size-5"
                        @checked(in_array($attachment->id, old('remove_attachments', [])))>
                    <span class="min-w-0 flex-1 truncate">
                        {{ $attachment->isLink() ? '▶ '.$attachment->url : '📎 '.$attachment->original_name }}
                    </span>
                    @unless ($attachment->isLink())
                        <span class="shrink-0 text-caption text-text-muted">{{ $attachment->readableSize() }}</span>
                    @endunless
                </label>
            @endforeach
        </div>
    </fieldset>
@endif
