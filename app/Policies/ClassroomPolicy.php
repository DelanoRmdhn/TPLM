<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;

/**
 * PB-05: guru hanya kelas yang dia ajar, siswa hanya kelas yang dia ikuti.
 * Admin tidak membuka kelas (tidak melihat nilai per siswa).
 */
class ClassroomPolicy
{
    /** Buka kelas dan lihat anggotanya. */
    public function view(User $user, Classroom $classroom): bool
    {
        if ($user->isGuru()) {
            return $classroom->isTaughtBy($user);
        }

        if ($user->isSiswa()) {
            return $classroom->hasStudent($user);
        }

        return false;
    }

    /** Ubah kelas, tambah rombel, buat materi/tugas. */
    public function manage(User $user, Classroom $classroom): bool
    {
        return $user->isGuru() && $classroom->isTaughtBy($user);
    }
}
