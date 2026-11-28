{{-- Lampiran guru pada tugas (file atau video YouTube). --}}
@if ($attachments->isNotEmpty())
    <div class="space-y-2">
        @foreach ($attachments as $attachment)
            <x-attachment-link :attachment="$attachment" />
        @endforeach
    </div>
@endif
