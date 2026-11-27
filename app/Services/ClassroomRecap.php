<?php

namespace App\Services;

use App\Enums\AssignmentStatus;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Rekap nilai satu kelas: baris = siswa anggota kelas, kolom = tugas (urut tenggat).
 * Rata-rata dihitung dari tugas yang sudah dinilai, dalam skala 100,
 * karena nilai maksimal tiap tugas bisa berbeda.
 * Dipakai halaman Rekap dan file Excel rekap.
 */
class ClassroomRecap
{
    /**
     * @return object{assignments: Collection<int, Assignment>, rows: Collection<int, object{student: User, cells: Collection<int, object{status: AssignmentStatus, grade: ?Grade}>, average: ?float}>}
     */
    public function for(Classroom $classroom): object
    {
        $assignments = $classroom->assignments()
            ->with(['grades', 'submissions' => fn ($q) => $q->whereNotNull('submitted_at')])
            ->orderBy('due_at')
            ->orderBy('id')
            ->get();

        $rows = $classroom->students()->orderBy('name')->get()->map(function (User $student) use ($assignments) {
            $cells = $assignments->mapWithKeys(function (Assignment $assignment) use ($student) {
                $submission = $assignment->submissions->firstWhere('student_id', $student->id);
                $grade = $assignment->grades->firstWhere('student_id', $student->id);

                return [$assignment->id => (object) [
                    'status' => AssignmentStatus::for($assignment, $submission, $grade),
                    'grade' => $grade,
                ]];
            });

            $scaled = $assignments
                ->filter(fn (Assignment $assignment) => $cells[$assignment->id]->grade !== null)
                ->map(fn (Assignment $assignment) => $cells[$assignment->id]->grade->score / $assignment->max_score * 100);

            return (object) [
                'student' => $student,
                'cells' => $cells,
                'average' => $scaled->isEmpty() ? null : round($scaled->avg(), 1),
            ];
        });

        return (object) ['assignments' => $assignments, 'rows' => $rows];
    }

    /**
     * Isi file Excel: Nama, NISN, satu kolom per tugas (nilai angka atau status), Rata-rata.
     *
     * @return array<int, array<int, string|int|float>>
     */
    public function spreadsheetRows(Classroom $classroom): array
    {
        $recap = $this->for($classroom);

        $header = ['Nama', 'NISN'];
        foreach ($recap->assignments as $assignment) {
            $header[] = "{$assignment->title} (maks {$assignment->max_score})";
        }
        $header[] = 'Rata-rata (skala 100)';

        $rows = [$header];
        foreach ($recap->rows as $row) {
            $line = [$row->student->name, $row->student->username];
            foreach ($recap->assignments as $assignment) {
                $cell = $row->cells[$assignment->id];
                $line[] = $cell->grade?->score ?? $cell->status->label();
            }
            $line[] = $row->average ?? '';
            $rows[] = $line;
        }

        return $rows;
    }
}
