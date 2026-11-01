<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\Rombel;
use App\Models\User;
use App\Support\SimpleXlsx;
use App\Support\TemporaryPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * FR-05, PB-02: impor akun siswa/guru dari Excel.
 * Akun dibuat dengan kata sandi awal yang wajib diganti saat login pertama.
 * Baris bermasalah (kosong, dobel, sudah terdaftar) dilaporkan, tidak dibuat.
 */
class AccountImporter
{
    public const MAX_ROWS = 1000;

    /** Judul kolom template, urutannya harus sama dengan file yang diimpor. */
    public const HEADERS = [
        'siswa' => ['Nama', 'NISN', 'Rombel'],
        'guru' => ['Nama', 'NIP atau Username'],
    ];

    /**
     * @return array{created: list<array{name: string, username: string, rombel: string, password: string}>, problems: list<array{row: int, message: string}>}
     */
    public function import(string $path, string $type): array
    {
        $rows = SimpleXlsx::read($path);
        unset($rows[1]); // baris 1 = judul kolom

        $valid = [];
        $problems = [];
        $seen = [];

        foreach ($rows as $rowNumber => $cells) {
            if (implode('', $cells) === '') {
                continue;
            }

            if (count($valid) + count($problems) >= self::MAX_ROWS) {
                $problems[] = ['row' => $rowNumber, 'message' => 'Melebihi batas '.self::MAX_ROWS.' baris per impor.'];
                break;
            }

            $parsed = $type === 'siswa' ? $this->parseSiswa($cells) : $this->parseGuru($cells);

            if (is_string($parsed)) {
                $problems[] = ['row' => $rowNumber, 'message' => $parsed];

                continue;
            }

            $key = strtolower($parsed['username']);
            if (isset($seen[$key])) {
                $problems[] = ['row' => $rowNumber, 'message' => "{$parsed['username']} dobel dengan baris {$seen[$key]}."];

                continue;
            }
            $seen[$key] = $rowNumber;

            $valid[$rowNumber] = $parsed;
        }

        // Cek yang sudah terdaftar di database
        $existing = User::whereIn('username', array_column($valid, 'username'))
            ->pluck('username')
            ->map(fn ($u) => strtolower($u))
            ->all();

        foreach ($valid as $rowNumber => $parsed) {
            if (in_array(strtolower($parsed['username']), $existing, true)) {
                $problems[] = ['row' => $rowNumber, 'message' => "{$parsed['username']} sudah terdaftar."];
                unset($valid[$rowNumber]);
            }
        }

        usort($problems, fn ($a, $b) => $a['row'] <=> $b['row']);

        // Buat akun. Hash dengan cost lebih ringan supaya impor ratusan akun tidak kelamaan;
        // aman karena kata sandi awal wajib diganti saat login pertama.
        @set_time_limit(300);
        $rounds = min(10, (int) config('hashing.bcrypt.rounds', 12));

        $created = DB::transaction(function () use ($valid, $type, $rounds) {
            $created = [];

            foreach ($valid as $parsed) {
                $password = TemporaryPassword::generate();
                $rombel = $type === 'siswa' ? Rombel::findOrCreateByName($parsed['rombel']) : null;

                User::create([
                    'name' => $parsed['name'],
                    'username' => $parsed['username'],
                    'role' => $type === 'siswa' ? Role::Siswa : Role::Guru,
                    'rombel_id' => $rombel?->id,
                    'is_active' => true,
                    'must_change_password' => true,
                    'password' => Hash::make($password, ['rounds' => $rounds]),
                ]);

                $created[] = [
                    'name' => $parsed['name'],
                    'username' => $parsed['username'],
                    'rombel' => $rombel?->name ?? '',
                    'password' => $password,
                ];
            }

            return $created;
        });

        return ['created' => $created, 'problems' => $problems];
    }

    /** @return array{name: string, username: string, rombel: string}|string */
    private function parseSiswa(array $cells): array|string
    {
        [$name, $nisn, $rombel] = array_pad($cells, 3, '');

        if ($name === '') {
            return 'Nama kosong.';
        }

        $nisn = self::normalizeNisn($nisn);

        if ($nisn === '') {
            return 'NISN kosong.';
        }

        if (stripos($nisn, 'e') !== false) {
            return 'NISN terbaca sebagai angka (mis. 1.23E+9). Ubah kolom NISN jadi Teks lalu ketik ulang.';
        }

        if (! preg_match('/^\d{10}$/', $nisn)) {
            return "NISN {$nisn} harus 10 digit angka.";
        }

        if ($rombel === '') {
            return 'Rombel kosong.';
        }

        return ['name' => $name, 'username' => $nisn, 'rombel' => $rombel];
    }

    /** @return array{name: string, username: string, rombel: string}|string */
    private function parseGuru(array $cells): array|string
    {
        [$name, $username] = array_pad($cells, 2, '');

        if ($name === '') {
            return 'Nama kosong.';
        }

        $username = str_replace(' ', '', $username);

        if ($username === '') {
            return 'NIP atau username kosong.';
        }

        if (preg_match('/^\d+(\.\d+)?E\+\d+$/i', $username)) {
            return 'NIP terbaca sebagai angka (mis. 1.98E+17). Ubah kolom NIP jadi Teks lalu ketik ulang.';
        }

        if (! preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
            return "Username {$username} hanya boleh huruf, angka, titik, strip, atau garis bawah (3–50 karakter).";
        }

        return ['name' => $name, 'username' => $username, 'rombel' => ''];
    }

    /** Excel sering membuang nol di depan NISN (0012345601 jadi 12345601). */
    public static function normalizeNisn(string $nisn): string
    {
        $nisn = str_replace(' ', '', $nisn);

        if (preg_match('/^\d{7,9}$/', $nisn)) {
            $nisn = str_pad($nisn, 10, '0', STR_PAD_LEFT);
        }

        return $nisn;
    }
}
