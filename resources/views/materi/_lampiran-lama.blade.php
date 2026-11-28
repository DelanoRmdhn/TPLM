{{-- Lampiran yang sudah ada di form ubah materi/tugas. Centang = hapus saat disimpan. --}}
@if ($attachments->isNotEmpty())
    <fieldset class="min-w-0">
        <legend class="mb-1 font-bold">Lampiran sekarang</legend>
        <p class="mb-2 text-caption text-text-muted">Centang yang mau dihapus.</p>
        <div class="space-y-2">
            @foreach ($attachments as $attachment)
                <label class="flex min-h-14 cursor-pointer items-center gap-3 rounded-control border-2 border-border bg-surface p-2 pr-3 has-checked:border-danger/40 has-checked:bg-danger-50">
                    <input type="checkbox" name="remove_attachments[]" value="{{ $attachment->id }}" class="peer size-5 accent-danger"
                        @checked(in_array($attachment->id, old('remove_attachments', [])))>
                    <x-icon :name="$attachment->isLink() ? 'youtube-logo' : ($attachment->isPdf() ? 'file-pdf' : ($attachment->isImage() ? 'image' : 'file-doc'))" class="size-6 text-brand" />
                    <span class="min-w-0 flex-1 truncate font-semibold peer-checked:text-danger peer-checked:line-through">
                        {{ $attachment->isLink() ? $attachment->url : $attachment->original_name }}
                    </span>
                    @unless ($attachment->isLink())
                        <span class="shrink-0 text-caption text-text-muted">{{ $attachment->readableSize() }}</span>
                    @endunless
                </label>
            @endforeach
        </div>
    </fieldset>
@endif
