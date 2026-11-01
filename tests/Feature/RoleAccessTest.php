<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_siswa_hanya_bisa_buka_halaman_siswa(): void
    {
        $siswa = User::factory()->create(['role' => Role::Siswa]);

        $this->actingAs($siswa)->get('/siswa/beranda')->assertOk();
        $this->actingAs($siswa)->get('/siswa/tugas')->assertOk();

        $this->actingAs($siswa)->get('/guru/beranda')->assertForbidden();
        $this->actingAs($siswa)->get('/guru/buat-kelas')->assertForbidden();
        $this->actingAs($siswa)->get('/admin/beranda')->assertForbidden();
    }

    public function test_guru_hanya_bisa_buka_halaman_guru(): void
    {
        $guru = User::factory()->create(['role' => Role::Guru]);

        $this->actingAs($guru)->get('/guru/beranda')->assertOk();
        $this->actingAs($guru)->get('/guru/buat-kelas')->assertOk();

        $this->actingAs($guru)->get('/siswa/beranda')->assertForbidden();
        $this->actingAs($guru)->get('/admin/beranda')->assertForbidden();
    }

    public function test_admin_hanya_bisa_buka_halaman_admin(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);

        $this->actingAs($admin)->get('/admin/beranda')->assertOk();

        $this->actingAs($admin)->get('/siswa/beranda')->assertForbidden();
        $this->actingAs($admin)->get('/guru/beranda')->assertForbidden();
    }
}