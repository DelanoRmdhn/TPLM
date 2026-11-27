<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Rombel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * S13, FR-09, FR-11, PB-07: anggota kelas, tambah satu rombel sekaligus, tambah/keluarkan satu siswa.
 */
class ClassroomMemberController extends Controller
{
    /** Guru dan siswa anggota kelas bisa melihat daftar anggota. */
    public function index(Request $request, Classroom $classroom): View
    {
        Gate::authorize('view', $classroom);

        $classroom->load(['teacher', 'subject', 'rombel']);

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

    /** Tambah satu siswa aktif lewat NISN, mis. siswa pindahan atau dari rombel lain. */
    public function store(Request $request, Classroom $classroom): RedirectResponse
    {
        Gate::authorize('manage', $classroom);

        $data = $request->validate([
            'nisn' => ['required', 'string', Rule::exists('users', 'username')->where('role', 'siswa')->where('is_active', true)],
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.exists' => 'Tidak ada siswa aktif dengan NISN ini.',
        ]);

        $student = User::where('username', $data['nisn'])->firstOrFail();

        if ($classroom->hasStudent($student)) {
            return back()->with('status', "{$student->name} sudah ada di kelas ini.");
        }

        $classroom->students()->attach($student);

        return back()->with('status', "{$student->name} ditambahkan ke kelas.");
    }

    /** Keluarkan siswa dari kelas. Pengumpulan dan nilainya tetap tersimpan kalau nanti dimasukkan lagi. */
    public function destroy(Classroom $classroom, User $student): RedirectResponse
    {
        Gate::authorize('manage', $classroom);

        $classroom->students()->detach($student);

        return back()->with('status', "{$student->name} dikeluarkan dari kelas.");
    }
}
