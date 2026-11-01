<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Services\StudentTasks;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * PB-08, FR-10, FR-26: beranda siswa berisi kelas yang diikuti dan tugas dengan tenggat terdekat.
 */
class BerandaController extends Controller
{
    public function __invoke(Request $request, StudentTasks $tasks): View
    {
        $user = $request->user()->load('rombel');
        $pending = $tasks->for($user)->reject->done->values();

        return view('siswa.beranda', [
            'classrooms' => $user->classrooms()->with('teacher')->orderBy('name')->get(),
            // Tenggat terdekat yang belum lewat
            'nearest' => $pending->first(fn ($row) => ! $row->assignment->isPastDue()),
            // "Belum dikerjakan": maks 3, termasuk yang terlambat
            'pending' => $pending->take(3),
            'pendingPerClass' => $pending->countBy(fn ($row) => $row->assignment->classroom_id),
        ]);
    }
}
