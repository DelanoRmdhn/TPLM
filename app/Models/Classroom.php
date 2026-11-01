<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['teacher_id', 'name', 'subject', 'rombel_id', 'academic_year'])]
class Classroom extends Model
{
    use HasFactory;

    /** Guru pembuat kelas. */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /** Rombel utama kelas (dipilih saat membuat kelas). */
    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }

    /** Anggota kelas: siswa. */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'classroom_members')->withTimestamps();
    }

    public function isTaughtBy(User $user): bool
    {
        return $this->teacher_id === $user->id;
    }

    public function hasStudent(User $user): bool
    {
        return $this->students()->whereKey($user->id)->exists();
    }

    /**
     * PB-07: masukkan semua siswa aktif dari satu rombel sekaligus.
     * Siswa yang sudah ada tidak dobel. Hasil: jumlah siswa yang baru masuk.
     */
    public function addRombel(Rombel $rombel): int
    {
        $ids = $rombel->students()
            ->where('role', 'siswa')
            ->where('is_active', true)
            ->pluck('id');

        return count($this->students()->syncWithoutDetaching($ids)['attached']);
    }

    /** Pilihan tahun ajaran: tahun ajaran berjalan (mulai Juli) dan berikutnya. */
    public static function academicYearOptions(): array
    {
        $start = now()->month >= 7 ? now()->year : now()->year - 1;

        return [$start.'/'.($start + 1), ($start + 1).'/'.($start + 2)];
    }
}
