<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Mata pelajaran, dikelola admin. Kelas menunjuk ke mapel lewat classrooms.subject_id,
 * jadi kalau admin mengganti nama, semua kelas langsung memakai nama baru.
 */
#[Fillable(['name'])]
class Subject extends Model
{
    /** Nama mapel untuk pilihan di form buat kelas, sesuai urutan dibuat. */
    public static function names(): Collection
    {
        return static::query()->orderBy('id')->pluck('name');
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }
}
