<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Materi (FR-14, FR-15). File dan tautan disimpan di tabel attachments.
 */
#[Fillable(['classroom_id', 'topic_id', 'author_id', 'title', 'description'])]
class Material extends Model
{
    protected static function booted(): void
    {
        // Lampiran polymorphic tidak punya foreign key, jadi dihapus manual (file ikut terhapus)
        static::deleting(fn (Material $material) => $material->attachments->each->delete());
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
}
