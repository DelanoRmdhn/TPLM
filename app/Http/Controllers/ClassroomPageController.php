<?php

namespace App\Http\Controllers;

use App\Enums\AssignmentStatus;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * M03a (guru) / M03b (siswa): halaman kelas berisi materi dan tugas per topik.
 */
class ClassroomPageController extends Controller
{
    public function show(Request $request, Classroom $classroom): View
    {
        Gate::authorize('view', $classroom);

        $classroom->load(['teacher', 'topics.materials.attachments', 'topics.assignments']);
        $canManage = Gate::allows('manage', $classroom);

        // Status tiap tugas untuk siswa yang sedang login (PB-13)
        $statuses = [];
        if (! $canManage) {
            $assignments = $classroom->assignments()
                ->with([
                    'submissions' => fn ($q) => $q->where('student_id', $request->user()->id),
                    'grades' => fn ($q) => $q->where('student_id', $request->user()->id),
                ])
                ->get();

            foreach ($assignments as $assignment) {
                $statuses[$assignment->id] = AssignmentStatus::for(
                    $assignment,
                    $assignment->submissions->first(),
                    $assignment->grades->first(),
                );
            }
        }

        return view('kelas.show', [
            'classroom' => $classroom,
            'canManage' => $canManage,
            'statuses' => $statuses,
            'withoutTopic' => $classroom->materials()->whereNull('topic_id')->with('attachments')->latest()->get(),
            'assignmentsWithoutTopic' => $classroom->assignments()->whereNull('topic_id')->latest()->get(),
        ]);
    }
}
