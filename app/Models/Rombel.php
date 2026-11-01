<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'grade_level'])]
class Rombel extends Model
{
    /** Siswa di rombel ini. */
    public function students(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** Cari rombel berdasarkan nama (mis. "4-A"), buat kalau belum ada. */
    public static function findOrCreateByName(string $name): self
    {
        $name = strtoupper(trim($name));
        $level = preg_match('/^(\d)/', $name, $m) ? (int) $m[1] : null;

        return static::firstOrCreate(['name' => $name], ['grade_level' => $level]);
    }
}
