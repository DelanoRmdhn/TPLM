<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 * Lampiran guru untuk materi (dan nanti tugas).
 * type = file (PDF/gambar/DOCX, disimpan privat) atau link (YouTube).
 */
#[Fillable(['type', 'path', 'original_name', 'mime_type', 'size', 'url'])]
class Attachment extends Model
{
    public const TYPE_FILE = 'file';

    public const TYPE_LINK = 'link';

    /** NFR-05: format dan ukuran yang diterima. */
    public const ALLOWED_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png', 'docx'];

    public const MAX_SIZE_KB = 10240;

    protected static function booted(): void
    {
        // Hapus file fisik saat lampiran dihapus
        static::deleted(function (Attachment $attachment) {
            if ($attachment->path) {
                Storage::disk('local')->delete($attachment->path);
            }
        });
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    /** Kelas pemilik lampiran (untuk cek akses). */
    public function classroom(): ?Classroom
    {
        return $this->attachable?->classroom;
    }

    public function isLink(): bool
    {
        return $this->type === self::TYPE_LINK;
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    /** ID video YouTube dari tautan, atau null kalau bukan YouTube. */
    public function youtubeId(): ?string
    {
        return self::parseYoutubeId((string) $this->url);
    }

    public static function parseYoutubeId(string $url): ?string
    {
        $pattern = '~^(?:https?://)?(?:www\.|m\.)?(?:youtube\.com/(?:watch\?(?:.*&)?v=|shorts/|embed/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~';

        return preg_match($pattern, trim($url), $m) ? $m[1] : null;
    }

    /** Ukuran file yang mudah dibaca, mis. "1,2 MB". */
    public function readableSize(): string
    {
        $kb = (int) $this->size / 1024;

        return $kb >= 1024
            ? number_format($kb / 1024, 1, ',', '.').' MB'
            : number_format(max($kb, 1), 0, ',', '.').' KB';
    }
}
