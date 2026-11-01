<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * FR-30, PB-31: status buka/tutup pendaftaran mandiri. Hanya satu baris.
 */
#[Fillable(['is_open', 'updated_by'])]
class RegistrationSetting extends Model
{
    protected function casts(): array
    {
        return ['is_open' => 'boolean'];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], ['is_open' => false]);
    }

    public static function isOpen(): bool
    {
        return static::current()->is_open;
    }
}
