<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Topik/bab dalam kelas, bisa diurutkan (PB-09).
 */
#[Fillable(['classroom_id', 'name', 'position'])]
class Topic extends Model
{
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class)->latest();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class)->latest();
    }
}
