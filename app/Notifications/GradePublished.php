<?php

namespace App\Notifications;

use App\Models\Grade;
use Illuminate\Notifications\Notification;

/** Untuk siswa: tugasnya dinilai atau nilainya diperbarui. */
class GradePublished extends Notification
{
    public function __construct(public Grade $grade, public bool $updated = false) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array{message: string, url: string} */
    public function toArray(object $notifiable): array
    {
        $assignment = $this->grade->assignment;
        $what = $this->updated ? 'Nilai diperbarui' : 'Tugas dinilai';

        return [
            'message' => "{$what}: {$assignment->title} · {$this->grade->score}/{$assignment->max_score}",
            'url' => route('kelas.tugas.show', [$assignment->classroom_id, $assignment]),
        ];
    }
}
