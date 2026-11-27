<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Admin melihat semua kelas dan memindahkan kelas ke guru lain (mis. guru pindah tugas).
 * Admin tidak membuka isi kelas atau nilai siswa (PB-05).
 */
class ClassroomController extends Controller
{
    public function index(Request $request): View
    {
        $archived = $request->query('status') === 'arsip';

        return view('admin.kelas.index', [
            'archived' => $archived,
            'classrooms' => Classroom::query()
                ->with(['teacher', 'subject', 'rombel'])
                ->withCount('students')
                ->when($archived, fn ($query) => $query->whereNotNull('archived_at'), fn ($query) => $query->notArchived())
                ->when($request->filled('q'), fn ($query) => $query->where(fn ($q) => $q
                    ->where('name', 'like', '%'.$request->q.'%')
                    ->orWhereHas('teacher', fn ($t) => $t->where('name', 'like', '%'.$request->q.'%'))))
                ->orderBy('name')
                ->paginate(25)
                ->withQueryString(),
            'teachers' => User::where('role', Role::Guru)->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /** Pindahkan kelas ke guru aktif lain. Materi, tugas, dan nilai tetap di kelas. */
    public function updateTeacher(Request $request, Classroom $classroom): RedirectResponse
    {
        $data = $request->validate([
            'teacher_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', Role::Guru->value)->where('is_active', true),
            ],
        ], [
            'teacher_id.required' => 'Pilih guru.',
            'teacher_id.exists' => 'Guru tidak ditemukan atau belum aktif.',
        ]);

        $classroom->update(['teacher_id' => $data['teacher_id']]);
        $teacher = User::find($data['teacher_id']);

        return back()->with('status', "Kelas {$classroom->name} sekarang diajar {$teacher->name}.");
    }
}
