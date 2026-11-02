<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Rombel;
use App\Models\Subject;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * FR-07, FR-10, PB-06, PB-07: guru membuat kelas dan melihat kelasnya di beranda.
 */
class ClassroomController extends Controller
{
    /** Beranda guru: daftar kelas yang diajar + pengumpulan yang perlu dinilai. */
    public function index(Request $request): View
    {
        $classrooms = $request->user()->teachingClassrooms()
            ->withCount('students')
            ->latest()
            ->get();

        // Pengumpulan yang sudah diserahkan tapi belum dinilai, dari siswa yang masih anggota kelas
        $ungraded = Submission::query()
            ->whereNotNull('submitted_at')
            ->join('assignments', 'assignments.id', '=', 'submissions.assignment_id')
            ->whereIn('assignments.classroom_id', $classrooms->pluck('id'))
            ->whereNotExists(fn ($q) => $q->select(DB::raw(1))->from('grades')
                ->whereColumn('grades.assignment_id', 'submissions.assignment_id')
                ->whereColumn('grades.student_id', 'submissions.student_id'))
            ->whereExists(fn ($q) => $q->select(DB::raw(1))->from('classroom_members')
                ->whereColumn('classroom_members.classroom_id', 'assignments.classroom_id')
                ->whereColumn('classroom_members.user_id', 'submissions.student_id'))
            ->orderBy('submissions.submitted_at')
            ->get(['submissions.*', 'assignments.classroom_id']);

        return view('guru.beranda', [
            'classrooms' => $classrooms,
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
            'subjects' => Subject::names(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:100'],
            'mata_pelajaran' => ['required', 'string', Rule::exists('subjects', 'name')],
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
                'subject' => $data['mata_pelajaran'],
                'rombel_id' => $rombel->id,
                'academic_year' => $data['tahun_ajaran'],
            ]);

            // Semua siswa rombel otomatis masuk (PB-07)
            return [$classroom, $classroom->addRombel($rombel)];
        });

        return redirect()->route('guru.beranda')
            ->with('status', "Kelas {$classroom->name} dibuat. {$added} siswa rombel {$rombel->name} sudah masuk.");
    }
}
