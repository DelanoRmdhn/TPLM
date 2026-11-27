<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Notifications\Notification;

/** Untuk siswa: guru menerbitkan tugas baru di kelasnya. */
class AssignmentPublished extends Notification
{
    public function __construct(public Assignment $assignment) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array{message: string, url: string} */
    public function toArray(object $notifiable): array
    {
        $classroom = $this->assignment->classroom;

        return [
            'message' => "Tugas baru di {$classroom->name}: {$this->assignment->title}",
            'url' => route('kelas.tugas.show', [$classroom, $this->assignment]),
        ];
    }
}
