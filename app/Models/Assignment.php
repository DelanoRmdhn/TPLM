<?php

namespace App\Models;

use App\Enums\AssignmentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

/**
 * Tugas (FR-16, PB-11). Lampiran guru di tabel attachments.
 */
#[Fillable(['classroom_id', 'topic_id', 'author_id', 'title', 'instructions', 'due_at', 'max_score'])]
class Assignment extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'max_score' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Assignment $assignment) {
            // Lampiran guru (polymorphic) + file pengumpulan siswa ikut dihapus dari penyimpanan
            $assignment->attachments->each->delete();
            Storage::disk('local')->delete(
                SubmissionFile::whereIn('submission_id', $assignment->submissions()->select('id'))->pluck('path')->all()
            );
        });
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function isPastDue(): bool
    {
        return $this->due_at !== null && $this->due_at->isPast();
    }

    /** Status untuk satu siswa (FR-20). */
    public function statusFor(User $student): AssignmentStatus
    {
        return AssignmentStatus::for(
            $this,
            $this->submissions()->where('student_id', $student->id)->first(),
            $this->grades()->where('student_id', $student->id)->first(),
        );
    }
}
