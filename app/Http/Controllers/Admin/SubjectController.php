<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Admin menambah, mengubah, dan menghapus mata pelajaran.
 * Mapel yang sudah dipakai kelas tidak bisa dihapus, hanya diubah namanya.
 */
class SubjectController extends Controller
{
    public function index(): View
    {
        return view('admin.mapel.index', [
            'subjects' => Subject::query()->withCount('classrooms')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Subject::create(['name' => $data['name']]);

        return redirect()->route('admin.mapel.index')->with('status', "Mata pelajaran {$data['name']} ditambahkan.");
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $data = $this->validated($request, $subject);
        $old = $subject->name;

        $subject->update(['name' => $data['name']]);

        return redirect()->route('admin.mapel.index')->with('status', "Mata pelajaran {$old} diubah jadi {$data['name']}.");
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $used = $subject->classrooms()->count();

        if ($used > 0) {
            return redirect()->route('admin.mapel.index')
                ->withErrors(['name' => "{$subject->name} dipakai di {$used} kelas, jadi tidak bisa dihapus. Ubah namanya saja kalau perlu."]);
        }

        $subject->delete();

        return redirect()->route('admin.mapel.index')->with('status', "Mata pelajaran {$subject->name} dihapus.");
    }

    private function validated(Request $request, ?Subject $subject = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('subjects', 'name')->ignore($subject)],
        ], [
            'name.required' => 'Nama mata pelajaran wajib diisi.',
            'name.max' => 'Nama mata pelajaran maksimal 100 karakter.',
            'name.unique' => 'Mata pelajaran ini sudah ada.',
        ]);
    }
}
