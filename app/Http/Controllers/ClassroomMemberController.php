<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Rombel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * S13, FR-09, FR-11, PB-07: anggota kelas dan tambah satu rombel sekaligus.
 */
class ClassroomMemberController extends Controller
{
    /** Guru dan siswa anggota kelas bisa melihat daftar anggota. */
    public function index(Request $request, Classroom $classroom): View
    {
        Gate::authorize('view', $classroom);

        $classroom->load(['teacher', 'rombel']);

        return view('kelas.anggota', [
            'classroom' => $classroom,
            'students' => $classroom->students()->with('rombel')->orderBy('name')->get(),
            'canManage' => Gate::allows('manage', $classroom),
            'rombels' => Rombel::withCount(['students' => fn ($q) => $q->where('is_active', true)])
                ->orderBy('name')
                ->get(),
        ]);
    }

    /** Pop-up tambah rombel: semua siswa aktif rombel itu masuk, tanpa dobel. */
    public function addRombel(Request $request, Classroom $classroom): RedirectResponse
    {
        Gate::authorize('manage', $classroom);

        $data = $request->validate([
            'rombel' => ['required', 'exists:rombels,id'],
        ], [
            'rombel.required' => 'Pilih rombel.',
            'rombel.exists' => 'Rombel tidak ditemukan.',
        ]);

        $rombel = Rombel::findOrFail($data['rombel']);
        $added = $classroom->addRombel($rombel);

        return back()->with('status', $added > 0
            ? "{$added} siswa rombel {$rombel->name} ditambahkan."
            : "Semua siswa rombel {$rombel->name} sudah ada di kelas ini.");
    }
}
