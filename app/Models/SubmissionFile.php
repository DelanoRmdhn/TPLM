<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Foto/file pengumpulan siswa. Disimpan privat (NFR-10).
 */
#[Fillable(['submission_id', 'path', 'original_name', 'mime_type', 'size'])]
class SubmissionFile extends Model
{
    /** NFR-05: JPG, PNG, PDF, DOCX, maks 10 MB per file. */
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'pdf', 'docx'];

    public const MAX_SIZE_KB = 10240;

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }
}
