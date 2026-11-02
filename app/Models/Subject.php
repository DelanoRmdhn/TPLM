<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Mata pelajaran, dikelola admin. Kelas menyimpan nama mapelnya (classrooms.subject),
 * jadi kalau admin mengganti nama, kelas yang memakai nama lama ikut diganti.
 */
#[Fillable(['name'])]
class Subject extends Model
{
    /** Nama mapel untuk pilihan di form buat kelas, sesuai urutan dibuat. */
    public static function names(): Collection
    {
        return static::query()->orderBy('id')->pluck('name');
    }

    public function classroomsCount(): int
    {
        return Classroom::where('subject', $this->name)->count();
    }
}
