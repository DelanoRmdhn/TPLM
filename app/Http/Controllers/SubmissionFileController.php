<?php

namespace App\Http\Controllers;

use App\Models\SubmissionFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Buka foto/file pengumpulan. Hanya siswa pemilik dan guru pengajar kelas (NFR-10).
 */
class SubmissionFileController extends Controller
{
    public function show(Request $request, SubmissionFile $file): StreamedResponse
    {
        $submission = $file->submission()->with('assignment.classroom')->firstOrFail();
        $user = $request->user();

        $allowed = $submission->student_id === $user->id
            || ($user->isGuru() && $submission->assignment->classroom->isTaughtBy($user));

        abort_unless($allowed, 403);
        abort_unless(Storage::disk('local')->exists($file->path), 404);

        $inline = ! $request->boolean('unduh') && ($file->isImage() || $file->isPdf());

        return Storage::disk('local')->response(
            $file->path,
            $file->original_name,
            [
                'Content-Type' => $file->mime_type,
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, max-age=3600',
            ],
            $inline ? 'inline' : 'attachment',
        );
    }
}
