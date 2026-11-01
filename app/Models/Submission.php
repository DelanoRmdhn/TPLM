<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Pengumpulan tugas siswa (FR-18, PB-12). Satu per siswa per tugas.
 */
#[Fillable(['assignment_id', 'student_id', 'body', 'submitted_at'])]
class Submission extends Model
{
    protected function casts(): array
    {
        return ['submitted_at' => 'datetime'];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    /** PB-14: diserahkan setelah tenggat. */
    public function isLate(): bool
    {
        $due = $this->assignment->due_at;

        return $this->submitted_at !== null && $due !== null && $this->submitted_at->greaterThan($due);
    }
}
