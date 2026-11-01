<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'role', 'rombel_id', 'is_active', 'must_change_password', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => Role::class,
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /** Rombel siswa. */
    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }

    /** Kelas yang diajar (guru). */
    public function teachingClassrooms(): HasMany
    {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }

    /** Kelas yang diikuti (siswa). */
    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'classroom_members')->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isGuru(): bool
    {
        return $this->role === Role::Guru;
    }

    public function isSiswa(): bool
    {
        return $this->role === Role::Siswa;
    }

    /** Nama route beranda sesuai peran, dipakai setelah login. */
    public function homeRoute(): string
    {
        return match ($this->role) {
            Role::Admin => 'admin.beranda',
            Role::Guru => 'guru.beranda',
            Role::Siswa => 'siswa.beranda',
        };
    }
}
