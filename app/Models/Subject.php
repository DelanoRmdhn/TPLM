<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Mata pelajaran, dikelola admin. Kelas menunjuk ke mapel lewat classrooms.subject_id,
 * jadi kalau admin mengganti nama, semua kelas langsung memakai nama baru.
 */
#[Fillable(['name'])]
class Subject extends Model
{
    /** Nama mapel untuk pilihan di form buat kelas, sesuai urutan dibuat. */
    public static function names(): Collection
    {
        return static::query()->orderBy('id')->pluck('name');
    }

    /** Urutan warna mapel (lihat --color-mapel-* di app.css). */
    public const COLORS = ['blue', 'green', 'orange', 'purple', 'pink', 'teal', 'red', 'amber'];

    /** Ikon Phosphor per kata kunci nama mapel. Mapel lain memakai ikon buku. */
    private const ICONS = [
        'matematika' => 'calculator',
        'bahasa indonesia' => 'book-open-text',
        'bahasa inggris' => 'translate',
        'ipa' => 'plant',
        'pjok' => 'soccer-ball',
        'seni' => 'palette',
        'agama' => 'hands-praying',
        'pancasila' => 'flag-banner',
        'muatan lokal' => 'map-trifold',
    ];

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    /** Warna tetap per mapel supaya siswa mudah mengenali kelasnya dari warna. */
    public function colorKey(): string
    {
        return self::COLORS[($this->id - 1) % count(self::COLORS)];
    }

    /**
     * Kelas Tailwind untuk blok berwarna penuh (kartu mapel, hero, lencana tanggal).
     * Ditulis lengkap supaya terbaca Tailwind. Amber memakai teks gelap supaya tetap kontras.
     */
    public function solidClasses(): string
    {
        return match ($this->colorKey()) {
            'blue' => 'bg-mapel-blue-solid text-white',
            'green' => 'bg-mapel-green-solid text-white',
            'orange' => 'bg-mapel-orange-solid text-white',
            'purple' => 'bg-mapel-purple-solid text-white',
            'pink' => 'bg-mapel-pink-solid text-white',
            'teal' => 'bg-mapel-teal-solid text-white',
            'red' => 'bg-mapel-red-solid text-white',
            'amber' => 'bg-mapel-amber-solid text-[#5c3d00]',
        };
    }

    /** Nama ikon Phosphor (tanpa awalan/varian), mis. "calculator". */
    public function iconName(): string
    {
        $name = strtolower($this->name);

        foreach (self::ICONS as $keyword => $icon) {
            if (str_contains($name, $keyword)) {
                return $icon;
            }
        }

        return 'books';
    }
}
