<?php

namespace App\Http\Controllers\Guru;

use App\Enums\AssignmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * M06, FR-23, PB-15: guru memberi nilai 0..nilai maksimal + umpan balik, status jadi "Dinilai".
 */
class GradeController extends Controller
{
    public function edit(Classroom $classroom, Assignment $assignment, User $student): View
    {
        $this->authorizeStudent($classroom, $assignment, $student);

        $submission = $assignment->submissions()->where('student_id', $student->id)->with('files')->first();
        $submission?->setRelation('assignment', $assignment);
        $grade = $assignment->grades()->where('student_id', $student->id)->first();

        return view('tugas.nilai', [
            'classroom' => $classroom,
            'assignment' => $assignment,
            'student' => $student,
            'submission' => $submission,
            'grade' => $grade,
            'status' => AssignmentStatus::for($assignment, $submission, $grade),
        ]);
    }

    public function update(Request $request, Classroom $classroom, Assignment $assignment, User $student): RedirectResponse
    {
        $this->authorizeStudent($classroom, $assignment, $student);

        $data = $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:'.$assignment->max_score],
            'feedback' => ['nullable', 'string', 'max:2000'],
        ], [
            'score.required' => 'Nilai wajib diisi.',
            'score.integer' => 'Nilai harus angka bulat.',
            'score.min' => 'Nilai paling kecil 0.',
            'score.max' => "Nilai paling besar {$assignment->max_score}.",
        ]);

        $assignment->grades()->updateOrCreate(
            ['student_id' => $student->id],
            ['score' => $data['score'], 'feedback' => $data['feedback'] ?? null, 'graded_by' => $request->user()->id],
        );

        // Lanjut ke pengumpulan berikutnya yang belum dinilai
        $next = $assignment->submissions()
            ->whereNotNull('submitted_at')
            ->whereNotIn('student_id', $assignment->grades()->select('student_id'))
            ->whereIn('student_id', $classroom->students()->select('users.id'))
            ->orderBy('submitted_at')
            ->value('student_id');

        $message = "Nilai {$student->name} disimpan.";

        return $next
            ? redirect()->route('kelas.tugas.nilai.edit', [$classroom, $assignment, $next])->with('status', $message.' Lanjut ke siswa berikutnya.')
            : redirect()->route('kelas.tugas.show', [$classroom, $assignment])->with('status', $message.' Semua pengumpulan sudah dinilai.');
    }

    /** Hanya guru pengajar; tugas harus milik kelas ini dan siswa harus anggota kelas. */
    private function authorizeStudent(Classroom $classroom, Assignment $assignment, User $student): void
    {
        abort_unless($assignment->classroom_id === $classroom->id, 404);
        Gate::authorize('manage', $classroom);
        abort_unless($classroom->hasStudent($student), 404);
    }
}
