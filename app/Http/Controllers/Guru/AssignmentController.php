<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MaterialController;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Services\AttachmentUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * M04: guru membuat tugas berisi judul, instruksi, lampiran, tenggat, nilai maksimal (FR-16, PB-11).
 */
class AssignmentController extends Controller
{
    public function create(Classroom $classroom): View
    {
        Gate::authorize('manage', $classroom);

        return view('tugas.create', [
            'classroom' => $classroom,
            'assignment' => new Assignment,
            'topics' => $classroom->topics()->get(),
        ]);
    }

    public function store(Request $request, Classroom $classroom, AttachmentUploader $uploader): RedirectResponse
    {
        Gate::authorize('manage', $classroom);

        $data = $this->validated($request, $classroom);
        $links = AttachmentUploader::parseLinks($data['links'] ?? null);
        $files = $request->file('files', []);

        try {
            $assignment = DB::transaction(function () use ($request, $classroom, $data, $files, $links, $uploader) {
                $assignment = $classroom->assignments()->create([
                    'topic_id' => MaterialController::resolveTopicId($classroom, $data),
                    'author_id' => $request->user()->id,
                    'title' => $data['title'],
                    'instructions' => $data['instructions'] ?? null,
                    'due_at' => Carbon::createFromFormat('Y-m-d\TH:i', $data['due_at']),
                    'max_score' => $data['max_score'],
                ]);

                $uploader->store($assignment, $files, $links, "tugas/{$classroom->id}");

                return $assignment;
            });
        } catch (\Throwable $e) {
            $uploader->cleanup();
            throw $e;
        }

        return redirect()->route('kelas.tugas.show', [$classroom, $assignment])
            ->with('status', 'Tugas berhasil diterbitkan.');
    }

    public function edit(Classroom $classroom, Assignment $assignment): View
    {
        Gate::authorize('manage', $classroom);

        $assignment->load('attachments');

        return view('tugas.create', [
            'classroom' => $classroom,
            'assignment' => $assignment,
            'topics' => $classroom->topics()->get(),
        ]);
    }

    /**
     * Status siswa (Belum dikerjakan/Terlambat) dihitung dari tenggat saat dibuka,
     * jadi kalau tenggat diubah, status ikut berubah sendiri.
     */
    public function update(Request $request, Classroom $classroom, Assignment $assignment, AttachmentUploader $uploader): RedirectResponse
    {
        Gate::authorize('manage', $classroom);

        $data = $this->validated($request, $classroom, $assignment);
        $links = AttachmentUploader::parseLinks($data['links'] ?? null);
        $files = $request->file('files', []);
        $remove = array_map('intval', $data['remove_attachments'] ?? []);

        try {
            DB::transaction(function () use ($classroom, $assignment, $data, $files, $links, $remove, $uploader) {
                $assignment->update([
                    'topic_id' => MaterialController::resolveTopicId($classroom, $data),
                    'title' => $data['title'],
                    'instructions' => $data['instructions'] ?? null,
                    'due_at' => Carbon::createFromFormat('Y-m-d\TH:i', $data['due_at']),
                    'max_score' => $data['max_score'],
                ]);

                $uploader->remove($assignment, $remove);
                $uploader->store($assignment, $files, $links, "tugas/{$classroom->id}");
            });
        } catch (\Throwable $e) {
            $uploader->cleanup();
            throw $e;
        }

        $uploader->purgeRemoved();

        return redirect()->route('kelas.tugas.show', [$classroom, $assignment])
            ->with('status', 'Tugas berhasil diubah.');
    }

    public function destroy(Classroom $classroom, Assignment $assignment): RedirectResponse
    {
        Gate::authorize('manage', $classroom);

        // Pengumpulan dan nilai siswa harus tetap ada
        if ($assignment->hasSubmissions()) {
            return redirect()->route('kelas.tugas.show', [$classroom, $assignment])
                ->withErrors(['tugas' => 'Tugas ini sudah dikumpulkan siswa, jadi tidak bisa dihapus.']);
        }

        $assignment->delete();

        return redirect()->route('kelas.show', $classroom)->with('status', "Tugas {$assignment->title} dihapus.");
    }

    /**
     * Saat mengubah: tenggat lama yang sudah lewat boleh dibiarkan, tapi tenggat baru harus setelah sekarang.
     * Nilai maksimal tidak boleh di bawah nilai yang sudah diberikan.
     */
    private function validated(Request $request, Classroom $classroom, ?Assignment $assignment = null): array
    {
        $dueUnchanged = $assignment !== null
            && $request->input('due_at') === $assignment->due_at?->format('Y-m-d\TH:i');
        $highestScore = $assignment ? (int) $assignment->grades()->max('score') : 0;

        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            'topic_id' => ['nullable', Rule::exists('topics', 'id')->where('classroom_id', $classroom->id)],
            'new_topic' => ['nullable', 'string', 'max:100'],
            'due_at' => ['required', 'date_format:Y-m-d\TH:i', ...($dueUnchanged ? [] : ['after:now'])],
            'max_score' => ['required', 'integer', 'min:'.max(1, $highestScore), 'max:100'],
            ...AttachmentUploader::rules(),
            ...($assignment ? AttachmentUploader::removalRules() : []),
        ], [
            'title.required' => 'Judul wajib diisi.',
            'topic_id.exists' => 'Topik tidak ditemukan.',
            'due_at.required' => 'Tenggat wajib diisi.',
            'due_at.date_format' => 'Format tenggat tidak valid.',
            'due_at.after' => 'Tenggat harus setelah waktu sekarang.',
            'max_score.required' => 'Nilai maksimal wajib diisi.',
            'max_score.integer' => 'Nilai maksimal harus angka 1 sampai 100.',
            'max_score.min' => $highestScore > 1
                ? "Nilai maksimal tidak boleh di bawah nilai yang sudah diberikan ({$highestScore})."
                : 'Nilai maksimal harus angka 1 sampai 100.',
            'max_score.max' => 'Nilai maksimal harus angka 1 sampai 100.',
            ...AttachmentUploader::messages(),
        ]);
    }
}
