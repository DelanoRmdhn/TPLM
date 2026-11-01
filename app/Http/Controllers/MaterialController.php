<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Material;
use App\Services\AttachmentUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * M04b buat materi, M03c/M03c2 buka materi (FR-14, FR-15, PB-09, PB-10).
 */
class MaterialController extends Controller
{
    public function create(Classroom $classroom): View
    {
        Gate::authorize('manage', $classroom);

        return view('materi.create', [
            'classroom' => $classroom,
            'topics' => $classroom->topics()->get(),
        ]);
    }

    public function store(Request $request, Classroom $classroom, AttachmentUploader $uploader): RedirectResponse
    {
        Gate::authorize('manage', $classroom);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'topic_id' => ['nullable', Rule::exists('topics', 'id')->where('classroom_id', $classroom->id)],
            'new_topic' => ['nullable', 'string', 'max:100'],
            ...AttachmentUploader::rules(),
        ], [
            'title.required' => 'Judul materi wajib diisi.',
            'topic_id.exists' => 'Topik tidak ditemukan.',
            ...AttachmentUploader::messages(),
        ]);

        $links = AttachmentUploader::parseLinks($data['links'] ?? null);
        $files = $request->file('files', []);

        if (blank($data['description'] ?? null) && $files === [] && $links->isEmpty()) {
            throw ValidationException::withMessages(['description' => 'Isi keterangan, unggah file, atau tambahkan tautan YouTube.']);
        }

        try {
            $material = DB::transaction(function () use ($request, $classroom, $data, $files, $links, $uploader) {
                $material = $classroom->materials()->create([
                    'topic_id' => $this->resolveTopicId($classroom, $data),
                    'author_id' => $request->user()->id,
                    'title' => $data['title'],
                    'description' => $data['description'] ?? null,
                ]);

                $uploader->store($material, $files, $links, "materi/{$classroom->id}");

                return $material;
            });
        } catch (\Throwable $e) {
            $uploader->cleanup();
            throw $e;
        }

        return redirect()->route('kelas.materi.show', [$classroom, $material])
            ->with('status', 'Materi berhasil dibagikan.');
    }

    public function show(Classroom $classroom, Material $material): View
    {
        Gate::authorize('view', $classroom);

        $material->load(['attachments', 'topic', 'author']);

        return view('materi.show', [
            'classroom' => $classroom,
            'material' => $material,
            'canManage' => Gate::allows('manage', $classroom),
        ]);
    }

    public function destroy(Classroom $classroom, Material $material): RedirectResponse
    {
        Gate::authorize('manage', $classroom);

        $material->delete();

        return redirect()->route('kelas.show', $classroom)->with('status', "Materi {$material->title} dihapus.");
    }

    /** Pakai topik yang dipilih, atau buat topik baru kalau diketik. */
    public static function resolveTopicId(Classroom $classroom, array $data): ?int
    {
        if (filled($data['new_topic'] ?? null)) {
            return $classroom->topics()->create([
                'name' => $data['new_topic'],
                'position' => (int) $classroom->topics()->max('position') + 1,
            ])->id;
        }

        return $data['topic_id'] ?? null;
    }
}
