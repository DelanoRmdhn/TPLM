{{--
    Kartu tugas siswa: lencana tanggal tenggat berwarna mapel di kiri, judul & status di kanan.
    $row dari App\Services\StudentTasks (assignment, submission, grade, status, dueLabel).
--}}
{{-- in-pending-list: kartu ada di daftar "belum dikerjakan", jadi badge "Belum dikerjakan" tidak perlu diulang (Terlambat tetap tampil) --}}
@props(['row', 'inPendingList' => false])

@php
    $assignment = $row->assignment;
    $subject = $assignment->classroom->subject;
    $late = $row->status === \App\Enums\AssignmentStatus::Terlambat;
    $showStatus = ! ($inPendingList && $row->status === \App\Enums\AssignmentStatus::BelumDikerjakan);
@endphp

<a href="{{ route('kelas.tugas.show', [$assignment->classroom_id, $assignment]) }}"
    {{ $attributes->class([
        'flex items-center gap-3.5 rounded-[22px] bg-surface p-3 pr-4 shadow-card transition active:scale-[0.99]',
        'ring-2 ring-danger/30' => $late,
    ]) }}>
    <x-date-badge :date="$assignment->due_at" :subject="$subject" />

    <div class="min-w-0 flex-1">
        <p class="truncate text-[12px] font-extrabold tracking-wide text-text-muted uppercase">{{ $subject->name }}</p>
        <h3 class="mt-0.5 leading-snug font-extrabold">{{ $assignment->title }}</h3>

        <div class="mt-2 flex flex-wrap items-center gap-1.5">
            @if ($showStatus)
                <x-status-badge :status="$row->status" class="px-2.5! py-0.5! text-[12px]!" />
            @endif

            @if ($row->grade)
                <span class="inline-flex items-center gap-1 text-[12px] font-extrabold text-success">
                    <x-icon name="star" variant="fill" class="size-3.5" />
                    Nilai {{ $row->grade->score }}/{{ $assignment->max_score }}
                </span>
            @elseif ($row->submission?->isSubmitted())
                <span class="inline-flex items-center gap-1 text-[12px] font-bold text-text-muted">
                    <x-icon name="calendar-check" class="size-3.5" />
                    {{ $row->submission->submitted_at->translatedFormat('j M, H.i') }}
                </span>
            @else
                <span @class(['inline-flex items-center gap-1 text-[12px] font-bold', 'text-danger' => $late, 'text-text-muted' => ! $late])>
                    <x-icon name="clock" class="size-3.5" />
                    {{ $row->dueLabel }}
                </span>
            @endif
        </div>
    </div>

    <x-icon name="caret-right" variant="bold" class="size-5 text-text-muted" />
</a>
