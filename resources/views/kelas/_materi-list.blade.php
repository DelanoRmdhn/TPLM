{{-- Daftar materi dalam satu topik — SEMENTARA dari BE --}}
<ul class="mt-2 divide-y divide-border">
    @forelse ($materials as $material)
        <li>
            <a href="{{ route('kelas.materi.show', [$classroom, $material]) }}" class="flex min-h-12 items-center justify-between gap-2 py-3">
                <span>
                    <span class="block font-semibold">📘 {{ $material->title }}</span>
                    <span class="text-caption text-text-muted">
                        Materi · {{ $material->created_at->translatedFormat('j M Y') }}
                        @if ($material->attachments->isNotEmpty()) · {{ $material->attachments->count() }} lampiran @endif
                    </span>
                </span>
                <span aria-hidden="true">›</span>
            </a>
        </li>
    @empty
        <li class="py-3 text-caption text-text-muted">Belum ada materi di topik ini.</li>
    @endforelse
</ul>
