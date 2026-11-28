<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Rombel;
use App\Models\Subject;
use App\Services\UngradedSubmissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * FR-07, FR-10, PB-06, PB-07: guru membuat kelas dan melihat kelasnya di beranda.
 */
class ClassroomController extends Controller
{
    /** Beranda guru: daftar kelas yang diajar + pengumpulan yang perlu dinilai. */
    public function index(Request $request, UngradedSubmissions $ungradedSubmissions): View
    {
        [$archived, $classrooms] = $request->user()->teachingClassrooms()
            ->with('subject')
            ->withCount('students')
            ->latest()
            ->get()
            ->partition->isArchived();

        $ungraded = $ungradedSubmissions->for($request->user());

        return view('guru.beranda', [
            'classrooms' => $classrooms->values(),
            'archivedClassrooms' => $archived->sortByDesc('archived_at')->values(),
            'ungradedCount' => $ungraded->count(),
            'ungradedPerClass' => $ungraded->countBy('classroom_id'),
            'oldestUngraded' => $ungraded->first(),
        ]);
    }

    /** Form buat kelas. Rombel diambil dari database beserta jumlah siswanya. */
    public function create(): View
    {
        return view('guru.buat-kelas', [
            'rombels' => Rombel::withCount(['students' => fn ($q) => $q->where('is_active', true)])
                ->orderBy('name')
                ->get(),
            'academicYears' => Classroom::academicYearOptions(),
            'subjects' => Subject::query()->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:100'],
            'mata_pelajaran' => ['required', 'integer', Rule::exists('subjects', 'id')],
            'rombel' => ['required', 'exists:rombels,id'],
            'tahun_ajaran' => ['required', Rule::in(Classroom::academicYearOptions())],
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'mata_pelajaran.required' => 'Pilih mata pelajaran.',
            'mata_pelajaran.exists' => 'Mata pelajaran tidak ada di daftar.',
            'rombel.required' => 'Pilih rombel.',
            'rombel.exists' => 'Rombel tidak ditemukan.',
            'tahun_ajaran.required' => 'Pilih tahun ajaran.',
            'tahun_ajaran.in' => 'Tahun ajaran tidak valid.',
        ]);

        $rombel = Rombel::findOrFail($data['rombel']);

        [$classroom, $added] = DB::transaction(function () use ($request, $data, $rombel) {
            $classroom = $request->user()->teachingClassrooms()->create([
                'name' => $data['nama_kelas'],
                'subject_id' => $data['mata_pelajaran'],
                'rombel_id' => $rombel->id,
                'academic_year' => $data['tahun_ajaran'],
            ]);

            // Semua siswa rombel otomatis masuk (PB-07)
            return [$classroom, $classroom->addRombel($rombel)];
        });

        return redirect()->route('guru.beranda')
            ->with('status', "Kelas {$classroom->name} dibuat. {$added} siswa rombel {$rombel->name} sudah masuk.");
    }

    /** Ubah nama, mapel, dan tahun ajaran kelas. */
    public function edit(Classroom $classroom): View
    {
        Gate::authorize('manage', $classroom);

        return view('kelas.ubah', [
            'classroom' => $classroom,
            'subjects' => Subject::query()->orderBy('id')->get(),
            'academicYears' => $this->academicYearsFor($classroom),
            'canDelete' => ! $classroom->hasStudentWork(),
        ]);
    }

    public function update(Request $request, Classroom $classroom): RedirectResponse
    {
        Gate::authorize('manage', $classroom);

        $data = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:100'],
            'mata_pelajaran' => ['required', 'integer', Rule::exists('subjects', 'id')],
            'tahun_ajaran' => ['required', Rule::in($this->academicYearsFor($classroom))],
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'mata_pelajaran.required' => 'Pilih mata pelajaran.',
            'mata_pelajaran.exists' => 'Mata pelajaran tidak ada di daftar.',
            'tahun_ajaran.required' => 'Pilih tahun ajaran.',
            'tahun_ajaran.in' => 'Tahun ajaran tidak valid.',
        ]);

        $classroom->update([
            'name' => $data['nama_kelas'],
            'subject_id' => $data['mata_pelajaran'],
            'academic_year' => $data['tahun_ajaran'],
        ]);

        return redirect()->route('kelas.show', $classroom)->with('status', 'Kelas berhasil diubah.');
    }

    /** Kelas selesai dipakai: disembunyikan dari beranda, isinya tetap bisa dilihat. */
    public function archive(Classroom $classroom): RedirectResponse
    {
        Gate::authorize('own', $classroom);

        $classroom->update(['archived_at' => now()]);

        return redirect()->route('guru.beranda')->with('status', "Kelas {$classroom->name} diarsipkan.");
    }

    public function restore(Classroom $classroom): RedirectResponse
    {
        Gate::authorize('own', $classroom);

        $classroom->update(['archived_at' => null]);

        return redirect()->route('kelas.show', $classroom)->with('status', "Kelas {$classroom->name} dipulihkan.");
    }

    /** Hanya kelas yang belum punya pengumpulan atau nilai siswa. Kalau sudah ada, arsipkan saja. */
    public function destroy(Classroom $classroom): RedirectResponse
    {
        Gate::authorize('own', $classroom);

        if ($classroom->hasStudentWork()) {
            return back()->withErrors(['kelas' => 'Kelas ini sudah punya pengumpulan atau nilai siswa, jadi tidak bisa dihapus. Arsipkan saja.']);
        }

        DB::transaction(function () use ($classroom) {
            // Lewat model supaya lampiran dan filenya ikut terhapus
            $classroom->materials()->get()->each->delete();
            $classroom->assignments()->get()->each->delete();
            $classroom->delete();
        });

        return redirect()->route('guru.beranda')->with('status', "Kelas {$classroom->name} dihapus.");
    }

    /** Pilihan tahun ajaran, termasuk tahun ajaran kelas yang sekarang walau sudah lewat. */
    private function academicYearsFor(Classroom $classroom): array
    {
        return array_values(array_unique([$classroom->academic_year, ...Classroom::academicYearOptions()]));
    }
}
