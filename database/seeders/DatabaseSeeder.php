<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Akun demo untuk development. JANGAN dijalankan di server.
 * Semua kata sandi: "password".
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'role' => Role::Admin,
        ]);

        User::factory()->create([
            'name' => 'Guru Demo',
            'username' => '198701012010011001', // NIP
            'role' => Role::Guru,
        ]);

        User::factory()->create([
            'name' => 'Siswa Demo',
            'username' => '0012345601', // NISN
            'role' => Role::Siswa,
        ]);

        // Untuk coba alur "wajib ganti kata sandi" (M01c)
        User::factory()->create([
            'name' => 'Siswa Baru',
            'username' => '0012345602', // NISN
            'role' => Role::Siswa,
            'must_change_password' => true,
        ]);
    }
}