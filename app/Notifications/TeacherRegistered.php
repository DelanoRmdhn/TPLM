<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

/** Untuk admin: ada guru yang mendaftar mandiri dan menunggu diaktifkan. */
class TeacherRegistered extends Notification
{
    public function __construct(public User $teacher) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array{message: string, url: string} */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "{$this->teacher->name} mendaftar sebagai guru dan menunggu diaktifkan",
            'url' => route('admin.akun.index', ['peran' => 'guru', 'status' => 'nonaktif']),
        ];
    }
}
