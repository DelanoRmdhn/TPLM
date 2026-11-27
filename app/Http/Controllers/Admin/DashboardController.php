<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\RegistrationSetting;
use App\Models\User;
use Illuminate\View\View;

/**
 * Beranda admin: ringkasan jumlah akun dan kelas, guru yang menunggu diaktifkan,
 * status pendaftaran, dan menu ke halaman kelola.
 */
class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $accounts = User::query()
            ->whereIn('role', [Role::Guru->value, Role::Siswa->value])
            ->selectRaw('role, is_active, count(*) as total')
            ->groupBy('role', 'is_active')
            ->get();

        $count = fn (Role $role, bool $active) => (int) $accounts
            ->first(fn ($row) => $row->role === $role && (bool) $row->is_active === $active)?->total;

        return view('admin.beranda', [
            'activeStudents' => $count(Role::Siswa, true),
            'activeTeachers' => $count(Role::Guru, true),
            'pendingTeachers' => $count(Role::Guru, false),
            'activeClassrooms' => Classroom::notArchived()->count(),
            'archivedClassrooms' => Classroom::whereNotNull('archived_at')->count(),
            'registrationOpen' => RegistrationSetting::current()->is_open,
        ]);
    }
}
