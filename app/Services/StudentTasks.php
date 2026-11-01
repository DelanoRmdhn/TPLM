<?php

namespace App\Services;

use App\Enums\AssignmentStatus;
use App\Models\Assignment;
use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Semua tugas dari kelas yang diikuti siswa, beserta status masing-masing.
 * Dipakai beranda siswa, Tugas Saya (FR-26, PB-17), dan Nilai Saya (FR-25, PB-16).
 */
class StudentTasks
{
    /**
     * @return Collection<int, object{assignment: Assignment, submission: ?Submission, grade: ?Grade, status: AssignmentStatus, done: bool, group: string, dueLabel: string}>
     */
    public function for(User $student): Collection
    {
        $classroomIds = $student->classrooms()->pluck('classrooms.id');

        return Assignment::query()
            ->whereIn('classroom_id', $classroomIds)
            ->with([
                'classroom',
                'submissions' => fn ($q) => $q->where('student_id', $student->id),
                'grades' => fn ($q) => $q->where('student_id', $student->id),
            ])
            ->orderBy('due_at')
            ->get()
            ->map(function (Assignment $assignment) {
                $submission = $assignment->submissions->first();
                $submission?->setRelation('assignment', $assignment);
                $grade = $assignment->grades->first();
                $status = AssignmentStatus::for($assignment, $submission, $grade);

                return (object) [
                    'assignment' => $assignment,
                    'submission' => $submission,
                    'grade' => $grade,
                    'status' => $status,
                    'done' => in_array($status, [AssignmentStatus::Diserahkan, AssignmentStatus::Dinilai], true),
                    'group' => self::group($assignment, $status),
                    'dueLabel' => self::dueLabel($assignment),
                ];
            });
    }

    /** Kelompok di Tugas Saya: Hari ini, Besok, Minggu ini, Nanti, Lewat tenggat. */
    public static function group(Assignment $assignment, AssignmentStatus $status): string
    {
        $due = $assignment->due_at;

        return match (true) {
            $status === AssignmentStatus::Terlambat => 'Lewat tenggat',
            $due->isToday() => 'Hari ini',
            $due->isTomorrow() => 'Besok',
            $due->lessThanOrEqualTo(now()->addDays(7)->endOfDay()) => 'Minggu ini',
            default => 'Nanti',
        };
    }

    /** Mis. "Hari ini, 12.00 WIB", "Besok, 23.59 WIB", "Kamis, 5 Nov, 23.59 WIB", "lewat 2 hari". */
    public static function dueLabel(Assignment $assignment): string
    {
        $due = $assignment->due_at;

        if ($due->isPast()) {
            return 'lewat '.$due->diffForHumans(now(), CarbonInterface::DIFF_ABSOLUTE);
        }

        $time = $due->format('H.i').' WIB';

        return match (true) {
            $due->isToday() => "Hari ini, {$time}",
            $due->isTomorrow() => "Besok, {$time}",
            default => $due->translatedFormat('l, j M').", {$time}",
        };
    }
}
