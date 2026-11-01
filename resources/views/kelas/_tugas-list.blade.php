{{-- Daftar tugas dalam satu topik — SEMENTARA dari BE --}}
@foreach ($assignments as $assignment)
    <li>
        <a href="{{ route('kelas.tugas.show', [$classroom, $assignment]) }}" class="flex min-h-12 items-center justify-between gap-2 py-3">
            <span>
                <span class="block font-semibold">📝 {{ $assignment->title }}</span>
                <span class="text-caption text-text-muted">
                    Tugas · Tenggat {{ $assignment->due_at?->translatedFormat('D, j M, H.i') }} WIB
                </span>
                @isset($statuses[$assignment->id])
                    <span class="mt-1 block w-fit rounded-full px-3 py-0.5 text-caption {{ $statuses[$assignment->id]->badgeClass() }}">
                        {{ $statuses[$assignment->id]->label() }}
                    </span>
                @endisset
            </span>
            <span aria-hidden="true">›</span>
        </a>
    </li>
@endforeach
