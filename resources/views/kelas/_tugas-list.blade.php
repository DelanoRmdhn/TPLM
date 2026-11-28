{{-- Daftar tugas dalam satu topik: lencana tanggal tenggat berwarna mapel. Untuk siswa, status tugasnya ikut tampil ($statuses). --}}
@foreach ($assignments as $assignment)
    <li>
        <a href="{{ route('kelas.tugas.show', [$classroom, $assignment]) }}"
            class="flex items-center gap-3.5 rounded-[22px] bg-surface p-3 pr-4 shadow-card transition active:scale-[0.99]">
            <x-date-badge :date="$assignment->due_at" :subject="$classroom->subject" />
            <span class="min-w-0 flex-1">
                <span class="block text-[12px] font-extrabold tracking-wide text-brand uppercase">Tugas</span>
                <span class="mt-0.5 block leading-snug font-extrabold">{{ $assignment->title }}</span>
                <span class="mt-1.5 flex flex-wrap items-center gap-1.5">
                    @isset($statuses[$assignment->id])
                        <x-status-badge :status="$statuses[$assignment->id]" class="px-2.5! py-0.5! text-[12px]!" />
                    @endisset
                    <span @class(['inline-flex items-center gap-1 text-[12px] font-bold', 'text-danger' => $assignment->isPastDue(), 'text-text-muted' => ! $assignment->isPastDue()])>
                        <x-icon name="clock" class="size-3.5" />{{ $assignment->due_at?->format('H.i') }} WIB
                    </span>
                </span>
            </span>
            <x-icon name="caret-right" variant="bold" class="size-5 text-text-muted" />
        </a>
    </li>
@endforeach
