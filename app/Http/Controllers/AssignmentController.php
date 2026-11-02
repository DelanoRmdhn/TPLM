<?php

namespace App\Http\Controllers;

use App\Enums\AssignmentStatus;
use App\Models\Assignment;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Buka tugas: guru → M06 pengumpulan & penilaian, siswa → M05 detail & kumpul.
 */
class AssignmentController extends Controller
{
    public function show(Request $request, Classroom $classroom, Assignment $assignment): View
    {
        Gate::authorize('view', $classroom);

        $assignment->load(['attachments', 'topic']);

        return Gate::allows('manage', $classroom)
            ? $this->forTeacher($classroom, $assignment)
            : $this->forStudent($request, $classroom, $assignment);
    }

    /** M06, FR-21, PB-14: siapa sudah & belum mengumpulkan, beserta jumlahnya. */
    private function forTeacher(Classroom $classroom, Assignment $assignment): View
    {
        $submissions = $assignment->submissions()->whereNotNull('submitted_at')->get()->keyBy('student_id');
        $grades = $assignment->grades()->get()->keyBy('student_id');

        $rows = $classroom->students()->orderBy('name')->get()->map(function ($student) use ($assignment, $submissions, $grades) {
            $submission = $submissions->get($student->id);
            $submission?->setRelation('assignment', $assignment);
            $grade = $grades->get($student->id);

            return (object) [
                'student' => $student,
                'submission' => $submission,
                'grade' => $grade,
                'status' => AssignmentStatus::for($assignment, $submission, $grade),
            ];
        });

        [$sudah, $belum] = $rows->partition(fn ($row) => $row->submission !== null || $row->grade !== null);

        return view('tugas.pengumpulan', [
            'classroom' => $classroom,
            'assignment' => $assignment,
            'sudah' => $sudah->values(),
            'belum' => $belum->values(),
            'canDelete' => $submissions->isEmpty() && ! $assignment->hasSubmissions(),
        ]);
    }

    /** M05, FR-18–FR-20: detail tugas, status, kumpul foto. */
    private function forStudent(Request $request, Classroom $classroom, Assignment $assignment): View
    {
        $student = $request->user();
        $submission = $assignment->submissions()->where('student_id', $student->id)->with('files')->first();
        $submission?->setRelation('assignment', $assignment);
        $grade = $assignment->grades()->where('student_id', $student->id)->first();

        return view('tugas.show', [
            'classroom' => $classroom,
            'assignment' => $assignment,
            'submission' => $submission,
            'grade' => $grade,
            'status' => AssignmentStatus::for($assignment, $submission, $grade),
        ]);
    }
}
