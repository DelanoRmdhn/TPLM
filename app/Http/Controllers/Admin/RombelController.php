<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rombel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Admin menambah, mengubah, dan menghapus rombel. Isi awal 1A–6B.
 * Rombel yang masih punya siswa atau kelas tidak bisa dihapus.
 */
class RombelController extends Controller
{
    public function index(): View
    {
        return view('admin.rombel.index', [
            'rombels' => Rombel::query()->withCount(['students', 'classrooms'])->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $name = $this->validated($request);

        Rombel::create(['name' => $name, 'grade_level' => Rombel::gradeLevelOf($name)]);

        return redirect()->route('admin.rombel.index')->with('status', "Rombel {$name} ditambahkan.");
    }

    /** Siswa dan kelas menempel lewat id, jadi ikut berganti nama sendiri. */
    public function update(Request $request, Rombel $rombel): RedirectResponse
    {
        $old = $rombel->name;
        $name = $this->validated($request, $rombel);

        $rombel->update(['name' => $name, 'grade_level' => Rombel::gradeLevelOf($name)]);

        return redirect()->route('admin.rombel.index')->with('status', "Rombel {$old} diubah jadi {$name}.");
    }

    public function destroy(Rombel $rombel): RedirectResponse
    {
        $students = $rombel->students()->count();
        $classrooms = $rombel->classrooms()->count();

        if ($students > 0 || $classrooms > 0) {
            $used = collect([$students > 0 ? "{$students} siswa" : null, $classrooms > 0 ? "{$classrooms} kelas" : null])
                ->filter()->implode(' dan ');

            return redirect()->route('admin.rombel.index')
                ->withErrors(['name' => "Rombel {$rombel->name} masih dipakai {$used}, jadi tidak bisa dihapus."]);
        }

        $rombel->delete();

        return redirect()->route('admin.rombel.index')->with('status', "Rombel {$rombel->name} dihapus.");
    }

    private function validated(Request $request, ?Rombel $rombel = null): string
    {
        $request->merge(['name' => Rombel::normalizeName((string) $request->input('name'))]);

        return $request->validate([
            'name' => ['required', 'string', 'max:20', 'regex:'.Rombel::NAME_PATTERN, Rule::unique('rombels', 'name')->ignore($rombel)],
        ], [
            'name.required' => 'Nama rombel wajib diisi.',
            'name.regex' => 'Nama rombel harus angka kelas 1–6 lalu huruf, misalnya 4A.',
            'name.unique' => 'Rombel ini sudah ada.',
        ])['name'];
    }
}
