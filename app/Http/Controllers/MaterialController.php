<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Classroom;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    public function store(Request $request, Classroom $classroom): RedirectResponse
    {
        Gate::authorize('manage', $classroom);

        $extensions = implode(',', Attachment::ALLOWED_EXTENSIONS);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'topic_id' => ['nullable', Rule::exists('topics', 'id')->where('classroom_id', $classroom->id)],
            'new_topic' => ['nullable', 'string', 'max:100'],
            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => ['file', "extensions:{$extensions}", "mimes:{$extensions}", 'max:'.Attachment::MAX_SIZE_KB],
            'links' => ['nullable', 'string', 'max:2000'],
        ], [
            'title.required' => 'Judul materi wajib diisi.',
            'topic_id.exists' => 'Topik tidak ditemukan.',
            'files.max' => 'Maksimal 10 file per materi.',
            'files.*.extensions' => 'File harus PDF, JPG, PNG, atau DOCX.',
            'files.*.mimes' => 'File harus PDF, JPG, PNG, atau DOCX.',
            'files.*.max' => 'Ukuran file maksimal 10 MB.',
            'files.*.uploaded' => 'File gagal diunggah. Ukuran file maksimal 10 MB.',
        ]);

        // Tautan YouTube: satu per baris
        $links = collect(preg_split('/\R/', (string) ($data['links'] ?? '')))
            ->map(fn ($l) => trim($l))
            ->filter();

        $invalid = $links->first(fn ($l) => Attachment::parseYoutubeId($l) === null);
        if ($invalid !== null) {
            throw ValidationException::withMessages(['links' => "Tautan bukan video YouTube: {$invalid}"]);
        }

        $files = $request->file('files', []);

        if (blank($data['description'] ?? null) && $files === [] && $links->isEmpty()) {
            throw ValidationException::withMessages(['description' => 'Isi keterangan, unggah file, atau tambahkan tautan YouTube.']);
        }

        $stored = [];

        try {
            $material = DB::transaction(function () use ($request, $classroom, $data, $files, $links, &$stored) {
                $topicId = $data['topic_id'] ?? null;

                if (filled($data['new_topic'] ?? null)) {
                    $topicId = $classroom->topics()->create([
                        'name' => $data['new_topic'],
                        'position' => (int) $classroom->topics()->max('position') + 1,
                    ])->id;
                }

                $material = $classroom->materials()->create([
                    'topic_id' => $topicId,
                    'author_id' => $request->user()->id,
                    'title' => $data['title'],
                    'description' => $data['description'] ?? null,
                ]);

                foreach ($files as $file) {
                    $name = Str::uuid().'.'.strtolower($file->getClientOriginalExtension());
                    $path = $file->storeAs("materi/{$classroom->id}", $name, 'local');
                    $stored[] = $path;

                    $material->attachments()->create([
                        'type' => Attachment::TYPE_FILE,
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ]);
                }

                foreach ($links as $url) {
                    $material->attachments()->create(['type' => Attachment::TYPE_LINK, 'url' => $url]);
                }

                return $material;
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($stored);
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
}
