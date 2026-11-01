<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * PB-08, FR-10: beranda siswa berisi kelas yang diikuti.
 */
class BerandaController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load('rombel');

        return view('siswa.beranda', [
            'classrooms' => $user->classrooms()->with('teacher')->orderBy('name')->get(),
        ]);
    }
}
