<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Services\StudentTasks;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Tugas Saya (FR-26, PB-17): daftar tugas diurutkan dari tenggat terdekat, lewat tenggat ditandai.
 * Nilai Saya (M07, FR-25, PB-16): hanya nilai milik sendiri.
 */
class TaskController extends Controller
{
    private const GROUP_ORDER = ['Hari ini', 'Besok', 'Minggu ini', 'Nanti', 'Lewat tenggat'];

    public function index(Request $request, StudentTasks $tasks): View
    {
        [$done, $pending] = $tasks->for($request->user())->partition->done;

        $groups = $pending->groupBy('group')
            ->sortBy(fn ($rows, $group) => array_search($group, self::GROUP_ORDER, true));

        return view('siswa.tugas', [
            'groups' => $groups,
            // Sudah selesai: yang terbaru dikumpulkan di atas
            'done' => $done->sortByDesc(fn ($row) => $row->grade?->updated_at ?? $row->submission?->submitted_at)->values(),
        ]);
    }

    public function grades(Request $request, StudentTasks $tasks): View
    {
        $graded = $tasks->for($request->user())
            ->filter(fn ($row) => $row->grade !== null)
            ->sortByDesc(fn ($row) => $row->grade->updated_at)
            ->groupBy(fn ($row) => $row->assignment->classroom->name);

        return view('siswa.nilai', ['classes' => $graded]);
    }
}
