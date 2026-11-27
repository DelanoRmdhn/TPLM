<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Services\UngradedSubmissions;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Menu Penilaian: semua pengumpulan yang belum dinilai dari semua kelas guru,
 * dikelompokkan per kelas lalu per tugas, yang paling lama dikirim di atas.
 */
class GradingController extends Controller
{
    public function index(Request $request, UngradedSubmissions $ungradedSubmissions): View
    {
        $ungraded = $ungradedSubmissions->for($request->user());

        return view('guru.penilaian', [
            'total' => $ungraded->count(),
            'classes' => $ungraded
                ->groupBy('classroom_id')
                ->map(fn ($rows) => (object) [
                    'classroom' => $rows->first()->assignment->classroom,
                    'assignments' => $rows->groupBy('assignment_id')->map(fn ($submissions) => (object) [
                        'assignment' => $submissions->first()->assignment,
                        'submissions' => $submissions->values(),
                    ])->values(),
                ])
                ->values(),
        ]);
    }
}
