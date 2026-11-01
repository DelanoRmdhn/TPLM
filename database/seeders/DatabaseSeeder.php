<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Rombel;
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
        $rombel4A = Rombel::findOrCreateByName('4-A');
        Rombel::findOrCreateByName('4-B');
        Rombel::findOrCreateByName('4-C');

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
            'rombel_id' => $rombel4A->id,
        ]);

        // Untuk coba alur "wajib ganti kata sandi" (M01c)
        User::factory()->create([
            'name' => 'Siswa Baru',
            'username' => '0012345602', // NISN
            'role' => Role::Siswa,
            'rombel_id' => $rombel4A->id,
            'must_change_password' => true,
        ]);
    }
}
