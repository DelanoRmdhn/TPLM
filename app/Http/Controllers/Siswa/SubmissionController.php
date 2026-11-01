<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\SubmissionFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * M05, FR-18, PB-12: siswa mengumpulkan foto dari kamera/galeri, file, atau teks.
 * Foto dikompres di HP sebelum diunggah (lihat tugas/show.blade.php), server menolak > 10 MB.
 */
class SubmissionController extends Controller
{
    public function store(Request $request, Classroom $classroom, Assignment $assignment): RedirectResponse
    {
        Gate::authorize('view', $classroom);
        $student = $request->user();

        $extensions = implode(',', SubmissionFile::ALLOWED_EXTENSIONS);

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:5000'],
            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => ['file', "extensions:{$extensions}", "mimes:{$extensions}", 'max:'.SubmissionFile::MAX_SIZE_KB],
        ], [
            'files.max' => 'Maksimal 10 foto/file.',
            'files.*.extensions' => 'File harus foto (JPG/PNG), PDF, atau DOCX.',
            'files.*.mimes' => 'File harus foto (JPG/PNG), PDF, atau DOCX.',
            'files.*.max' => 'Ukuran file maksimal 10 MB.',
            'files.*.uploaded' => 'File gagal diunggah. Ukuran file maksimal 10 MB.',
        ]);

        $files = $request->file('files', []);

        if ($files === [] && blank($data['body'] ?? null)) {
            throw ValidationException::withMessages(['files' => 'Ambil foto tugasmu atau tulis jawaban dulu.']);
        }

        if ($assignment->grades()->where('student_id', $student->id)->exists()) {
            throw ValidationException::withMessages(['files' => 'Tugas ini sudah dinilai.']);
        }

        $existing = $assignment->submissions()->where('student_id', $student->id)->first();
        if ($existing?->isSubmitted()) {
            throw ValidationException::withMessages(['files' => 'Tugas ini sudah kamu serahkan.']);
        }

        $stored = [];

        try {
            DB::transaction(function () use ($assignment, $student, $data, $files, $existing, &$stored) {
                $submission = $existing ?? $assignment->submissions()->make(['student_id' => $student->id]);
                $submission->fill(['body' => $data['body'] ?? null, 'submitted_at' => now()])->save();

                foreach ($files as $file) {
                    $path = $file->storeAs(
                        "pengumpulan/{$assignment->id}/{$student->id}",
                        Str::uuid().'.'.strtolower($file->getClientOriginalExtension()),
                        'local',
                    );
                    $stored[] = $path;

                    $submission->files()->create([
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ]);
                }
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($stored);
            throw $e;
        }

        return back()->with('status', $assignment->isPastDue()
            ? 'Tugas diserahkan (terlambat). Guru tetap bisa menilainya.'
            : 'Tugas berhasil diserahkan!');
    }
}
