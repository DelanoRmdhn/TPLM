<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Simpan lampiran guru (file + tautan YouTube) untuk materi atau tugas.
 */
class AttachmentUploader
{
    /** @var list<string> path file yang sudah disimpan, untuk dibersihkan kalau gagal */
    private array $stored = [];

    /** @var list<string> path file lampiran yang dihapus, dibersihkan setelah transaksi berhasil */
    private array $removed = [];

    /** Aturan validasi untuk input files[] dan links. */
    public static function rules(): array
    {
        $extensions = implode(',', Attachment::ALLOWED_EXTENSIONS);

        return [
            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => ['file', "extensions:{$extensions}", "mimes:{$extensions}", 'max:'.Attachment::MAX_SIZE_KB],
            'links' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public static function messages(): array
    {
        return [
            'files.max' => 'Maksimal 10 file.',
            'files.*.extensions' => 'File harus PDF, JPG, PNG, atau DOCX.',
            'files.*.mimes' => 'File harus PDF, JPG, PNG, atau DOCX.',
            'files.*.max' => 'Ukuran file maksimal 10 MB.',
            'files.*.uploaded' => 'File gagal diunggah. Ukuran file maksimal 10 MB.',
        ];
    }

    /** Tautan YouTube, satu per baris. Gagal validasi kalau ada yang bukan YouTube. */
    public static function parseLinks(?string $text): Collection
    {
        $links = collect(preg_split('/\R/', (string) $text))->map(fn ($l) => trim($l))->filter()->values();

        $invalid = $links->first(fn ($l) => Attachment::parseYoutubeId($l) === null);
        if ($invalid !== null) {
            throw ValidationException::withMessages(['links' => "Tautan bukan video YouTube: {$invalid}"]);
        }

        return $links;
    }

    /**
     * @param  Model  $owner  Material atau Assignment (punya relasi attachments())
     * @param  array<int, UploadedFile>  $files
     */
    public function store(Model $owner, array $files, Collection $links, string $directory): void
    {
        foreach ($files as $file) {
            $path = $file->storeAs($directory, Str::uuid().'.'.strtolower($file->getClientOriginalExtension()), 'local');
            $this->stored[] = $path;

            $owner->attachments()->create([
                'type' => Attachment::TYPE_FILE,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        foreach ($links as $url) {
            $owner->attachments()->create(['type' => Attachment::TYPE_LINK, 'url' => $url]);
        }
    }

    /** Aturan untuk centang "hapus lampiran ini" di form ubah. */
    public static function removalRules(): array
    {
        return [
            'remove_attachments' => ['nullable', 'array'],
            'remove_attachments.*' => ['integer'],
        ];
    }

    /**
     * Hapus baris lampiran yang dicentang (hanya milik $owner).
     * File fisiknya baru dihapus lewat purgeRemoved() setelah transaksi berhasil,
     * supaya file tidak hilang kalau penyimpanan gagal di tengah jalan.
     */
    public function remove(Model $owner, array $ids): void
    {
        if ($ids === []) {
            return;
        }

        $query = $owner->attachments()->whereIn('id', $ids);
        $this->removed = array_merge($this->removed, (clone $query)->whereNotNull('path')->pluck('path')->all());
        $query->delete();
    }

    /** Hapus file fisik lampiran yang sudah dihapus lewat remove(). */
    public function purgeRemoved(): void
    {
        Storage::disk('local')->delete($this->removed);
        $this->removed = [];
    }

    /** Hapus file yang sudah tersimpan (dipanggil kalau transaksi gagal). */
    public function cleanup(): void
    {
        Storage::disk('local')->delete($this->stored);
        $this->stored = [];
    }
}
