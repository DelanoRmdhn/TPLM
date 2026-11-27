<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Notifications\Notification;

/** Untuk guru: siswa menyerahkan tugas. */
class SubmissionReceived extends Notification
{
    public function __construct(public Submission $submission) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array{message: string, url: string} */
    public function toArray(object $notifiable): array
    {
        $assignment = $this->submission->assignment;
        $late = $this->submission->isLate() ? ' (terlambat)' : '';

        return [
            'message' => "{$this->submission->student->name} menyerahkan {$assignment->title}{$late}",
            'url' => route('kelas.tugas.nilai.edit', [$assignment->classroom_id, $assignment, $this->submission->student]),
        ];
    }
}
