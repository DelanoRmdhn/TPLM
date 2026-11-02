<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Rombel (rombongan belajar), mis. "4A". Daftarnya dikelola admin.
 * Format nama selalu angka kelas + huruf tanpa strip, supaya "4-A", "4a", dan "4 A" dianggap sama.
 */
#[Fillable(['name', 'grade_level'])]
class Rombel extends Model
{
    /** Angka kelas 1–6 lalu huruf, mis. 4A. */
    public const NAME_PATTERN = '/^[1-6][A-Z]{1,3}$/';

    /** Siswa di rombel ini. */
    public function students(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    /** "4-a", " 4 A ", "4.A" → "4A" */
    public static function normalizeName(string $name): string
    {
        return strtoupper(preg_replace('/[\s\-._\/]+/', '', $name));
    }

    public static function gradeLevelOf(string $name): ?int
    {
        return preg_match('/^(\d)/', $name, $m) ? (int) $m[1] : null;
    }

    /** Cari rombel yang sudah ada di daftar. */
    public static function findByName(string $name): ?self
    {
        return static::where('name', static::normalizeName($name))->first();
    }

    /** Untuk seeder dan test: cari rombel, buat kalau belum ada. */
    public static function findOrCreateByName(string $name): self
    {
        $name = static::normalizeName($name);

        return static::firstOrCreate(['name' => $name], ['grade_level' => static::gradeLevelOf($name)]);
    }
}
