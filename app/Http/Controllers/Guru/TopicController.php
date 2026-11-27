<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * PB-09: topik bisa dibuat, diurutkan, diganti nama, dan dihapus.
 */
class TopicController extends Controller
{
    public function store(Request $request, Classroom $classroom): RedirectResponse
    {
        Gate::authorize('manage', $classroom);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ], [
            'name.required' => 'Nama topik wajib diisi.',
        ]);

        $classroom->topics()->create([
            'name' => $data['name'],
            'position' => (int) $classroom->topics()->max('position') + 1,
        ]);

        return back()->with('status', "Topik {$data['name']} ditambahkan.");
    }

    public function update(Request $request, Classroom $classroom, Topic $topic): RedirectResponse
    {
        Gate::authorize('manage', $classroom);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ], [
            'name.required' => 'Nama topik wajib diisi.',
        ]);

        $topic->update(['name' => $data['name']]);

        return back()->with('status', 'Nama topik diubah.');
    }

    /** Tugas dan materi di topik ini tidak ikut terhapus, pindah ke "Tanpa topik". */
    public function destroy(Classroom $classroom, Topic $topic): RedirectResponse
    {
        Gate::authorize('manage', $classroom);

        $topic->delete();

        return back()->with('status', "Topik {$topic->name} dihapus. Tugas dan materinya pindah ke Tanpa topik.");
    }

    /** Naikkan/turunkan topik satu tingkat. */
    public function move(Request $request, Classroom $classroom, Topic $topic, string $direction): RedirectResponse
    {
        Gate::authorize('manage', $classroom);
        abort_unless(in_array($direction, ['naik', 'turun'], true), 404);

        DB::transaction(function () use ($classroom, $topic, $direction) {
            // Rapikan nomor urut dulu (1, 2, 3, ...) supaya tukar posisi selalu benar
            $topics = $classroom->topics()->get()->values();
            $topics->each(fn (Topic $t, int $i) => $t->update(['position' => $i + 1]));

            $index = $topics->search(fn (Topic $t) => $t->is($topic));
            $swapIndex = $direction === 'naik' ? $index - 1 : $index + 1;

            if ($index === false || ! isset($topics[$swapIndex])) {
                return;
            }

            $other = $topics[$swapIndex];
            [$a, $b] = [$topics[$index]->position, $other->position];
            $topics[$index]->update(['position' => $b]);
            $other->update(['position' => $a]);
        });

        return back();
    }
}
