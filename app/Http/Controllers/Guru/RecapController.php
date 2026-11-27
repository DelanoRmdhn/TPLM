<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Services\ClassroomRecap;
use App\Support\SimpleXlsx;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Menu Rekap: rekap nilai per kelas (siswa × tugas) dan unduh sebagai Excel.
 * Kelas yang diarsipkan tetap bisa direkap.
 */
class RecapController extends Controller
{
    private const XLSX = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    /** Pilih kelas yang mau direkap. */
    public function index(Request $request): View
    {
        [$archived, $active] = $request->user()->teachingClassrooms()
            ->with('subject')
            ->withCount(['students', 'assignments'])
            ->orderBy('name')
            ->get()
            ->partition->isArchived();

        return view('guru.rekap', [
            'classrooms' => $active->values(),
            'archivedClassrooms' => $archived->values(),
        ]);
    }

    public function show(Classroom $classroom, ClassroomRecap $recap): View
    {
        Gate::authorize('own', $classroom);

        return view('kelas.rekap', [
            'classroom' => $classroom->load('subject'),
            'recap' => $recap->for($classroom),
        ]);
    }

    public function download(Classroom $classroom, ClassroomRecap $recap): Response
    {
        Gate::authorize('own', $classroom);

        $filename = 'rekap-nilai-'.Str::slug($classroom->name).'-'.now()->format('Y-m-d').'.xlsx';

        return response(SimpleXlsx::write($recap->spreadsheetRows($classroom), 'Rekap Nilai'), 200, [
            'Content-Type' => self::XLSX,
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
