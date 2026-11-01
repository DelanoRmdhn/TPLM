<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * PB-10: buka/unduh file materi. File disimpan privat (bukan di folder public),
 * hanya bisa dibuka anggota kelas (NFR-10).
 */
class AttachmentController extends Controller
{
    public function show(Request $request, Attachment $attachment): StreamedResponse
    {
        abort_if($attachment->isLink(), 404);

        $classroom = $attachment->classroom();
        abort_if($classroom === null, 404);
        Gate::authorize('view', $classroom);

        abort_unless(Storage::disk('local')->exists($attachment->path), 404);

        // PDF & gambar dibuka langsung di browser HP, DOCX atau ?unduh=1 diunduh
        $inline = ! $request->boolean('unduh') && ($attachment->isPdf() || $attachment->isImage());

        return Storage::disk('local')->response(
            $attachment->path,
            $attachment->original_name,
            [
                'Content-Type' => $attachment->mime_type,
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, max-age=3600',
            ],
            $inline ? 'inline' : 'attachment',
        );
    }
}
