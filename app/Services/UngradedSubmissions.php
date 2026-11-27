<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Pengumpulan yang sudah diserahkan tapi belum dinilai, dari kelas guru yang belum diarsipkan
 * dan dari siswa yang masih anggota kelas. Dipakai beranda guru dan halaman Penilaian.
 */
class UngradedSubmissions
{
    /**
     * Urut dari yang paling lama dikirim. Tiap baris punya classroom_id, plus relasi assignment.classroom dan student.
     *
     * @return Collection<int, Submission>
     */
    public function for(User $teacher): Collection
    {
        return Submission::query()
            ->whereNotNull('submitted_at')
            ->join('assignments', 'assignments.id', '=', 'submissions.assignment_id')
            ->whereIn('assignments.classroom_id', $teacher->teachingClassrooms()->notArchived()->select('id'))
            ->whereNotExists(fn ($q) => $q->select(DB::raw(1))->from('grades')
                ->whereColumn('grades.assignment_id', 'submissions.assignment_id')
                ->whereColumn('grades.student_id', 'submissions.student_id'))
            ->whereExists(fn ($q) => $q->select(DB::raw(1))->from('classroom_members')
                ->whereColumn('classroom_members.classroom_id', 'assignments.classroom_id')
                ->whereColumn('classroom_members.user_id', 'submissions.student_id'))
            ->with(['assignment.classroom', 'student'])
            ->orderBy('submissions.submitted_at')
            ->get(['submissions.*', 'assignments.classroom_id']);
    }
}
