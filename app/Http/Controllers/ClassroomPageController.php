<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * M03a (guru) / M03b (siswa): halaman kelas berisi materi per topik.
 */
class ClassroomPageController extends Controller
{
    public function show(Classroom $classroom): View
    {
        Gate::authorize('view', $classroom);

        $classroom->load([
            'teacher',
            'topics.materials.attachments',
        ]);

        return view('kelas.show', [
            'classroom' => $classroom,
            'canManage' => Gate::allows('manage', $classroom),
            'withoutTopic' => $classroom->materials()->whereNull('topic_id')->with('attachments')->latest()->get(),
        ]);
    }
}
