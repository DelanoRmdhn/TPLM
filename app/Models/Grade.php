<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Nilai + umpan balik (FR-23, PB-15). Ada baris nilai = status "Dinilai".
 */
#[Fillable(['assignment_id', 'student_id', 'graded_by', 'score', 'feedback'])]
class Grade extends Model
{
    protected function casts(): array
    {
        return ['score' => 'integer'];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
