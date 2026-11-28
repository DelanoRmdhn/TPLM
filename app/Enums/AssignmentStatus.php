<?php

namespace App\Enums;

use App\Models\Assignment;
use App\Models\Grade;
use App\Models\Submission;

/**
 * FR-20, PB-13: empat status tugas siswa.
 * Terlambat = lewat tenggat dan belum diserahkan.
 * Yang diserahkan setelah tenggat tetap "Diserahkan", ditandai lewat Submission::isLate() (PB-14).
 */
enum AssignmentStatus: string
{
    case BelumDikerjakan = 'belum_dikerjakan';
    case Terlambat = 'terlambat';
    case Diserahkan = 'diserahkan';
    case Dinilai = 'dinilai';

    public static function for(Assignment $assignment, ?Submission $submission, ?Grade $grade): self
    {
        if ($grade !== null) {
            return self::Dinilai;
        }

        if ($submission?->isSubmitted()) {
            return self::Diserahkan;
        }

        return $assignment->isPastDue() ? self::Terlambat : self::BelumDikerjakan;
    }

    public function label(): string
    {
        return match ($this) {
            self::BelumDikerjakan => 'Belum dikerjakan',
            self::Terlambat => 'Terlambat',
            self::Diserahkan => 'Diserahkan',
            self::Dinilai => 'Dinilai',
        };
    }

    /** Warna badge (dibedakan warna + teks + ikon, PB-13). */
    public function badgeClass(): string
    {
        return match ($this) {
            self::BelumDikerjakan => 'bg-sunny-50 text-[#8a5a00]',
            self::Terlambat => 'bg-danger-50 text-danger',
            self::Diserahkan => 'bg-brand-50 text-brand-700',
            self::Dinilai => 'bg-success-50 text-success',
        };
    }

    /** Ikon Phosphor pendamping label status. */
    public function icon(): string
    {
        return match ($this) {
            self::BelumDikerjakan => 'hourglass-medium',
            self::Terlambat => 'warning-circle',
            self::Diserahkan => 'paper-plane-tilt',
            self::Dinilai => 'check-circle',
        };
    }
}
