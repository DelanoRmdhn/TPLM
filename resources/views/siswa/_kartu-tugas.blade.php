{{-- Kartu tugas siswa (gaya dari slicing Delano). $row dari App\Services\StudentTasks --}}
@php
    $warna = match ($row->status) {
        \App\Enums\AssignmentStatus::BelumDikerjakan => ['ikon' => 'bg-amber-100 text-amber-700', 'badge' => 'bg-amber-100 text-amber-700', 'titik' => 'bg-amber-700'],
        \App\Enums\AssignmentStatus::Terlambat => ['ikon' => 'bg-red-100 text-red-700', 'badge' => 'bg-red-100 text-red-700', 'titik' => 'bg-red-700'],
        \App\Enums\AssignmentStatus::Diserahkan => ['ikon' => 'bg-blue-100 text-blue-700', 'badge' => 'bg-blue-100 text-blue-700', 'titik' => 'bg-blue-700'],
        \App\Enums\AssignmentStatus::Dinilai => ['ikon' => 'bg-green-100 text-green-700', 'badge' => 'bg-green-100 text-green-700', 'titik' => 'bg-green-700'],
    };
@endphp

<a href="{{ route('kelas.tugas.show', [$row->assignment->classroom_id, $row->assignment]) }}"
    class="flex items-start gap-3.5 rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-blue-300">

    <div class="flex size-[50px] shrink-0 items-center justify-center rounded-xl {{ $warna['ikon'] }}">
        <svg viewBox="0 0 24 24" class="size-6"
            fill="none" stroke="currentColor" stroke-width="1.8"
            stroke-linecap="round" stroke-linejoin="round"
            aria-hidden="true">
            <rect x="5" y="4" width="14" height="17" rx="2"/>
            <path d="M9 4.5V3h6v1.5"/>
            <path d="m9 12 2 2 4-4"/>
        </svg>
    </div>

    <div class="min-w-0 flex-1">
        <h3 class="text-[15px] leading-5 font-bold">
            {{ $row->assignment->title }}
        </h3>

        <p class="mt-2 text-sm leading-5 text-slate-600">
            {{ $row->assignment->classroom->subject }} ·
            @if ($row->grade)
                Nilai {{ $row->grade->score }}/{{ $row->assignment->max_score }}
            @elseif ($row->submission?->isSubmitted())
                Dikumpulkan {{ $row->submission->submitted_at->translatedFormat('j M, H.i') }}
            @else
                {{ $row->dueLabel }}
            @endif
        </p>

        <span class="mt-1.5 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $warna['badge'] }}">
            <span class="size-2 rounded-full {{ $warna['titik'] }}"></span>
            {{ $row->status->label() }}
        </span>
    </div>

</a>
