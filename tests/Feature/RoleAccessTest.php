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
        $this->actingAs($siswa)->get('/admin/akun')->assertForbidden();
    }

    public function test_guru_hanya_bisa_buka_halaman_guru(): void
    {
        $guru = User::factory()->create(['role' => Role::Guru]);

        $this->actingAs($guru)->get('/guru/beranda')->assertOk();
        $this->actingAs($guru)->get('/guru/buat-kelas')->assertOk();

        $this->actingAs($guru)->get('/siswa/beranda')->assertForbidden();
        $this->actingAs($guru)->get('/admin/akun')->assertForbidden();
    }

    public function test_admin_hanya_bisa_buka_halaman_admin(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);

        $this->actingAs($admin)->get('/admin/beranda')->assertRedirect('/admin/akun');
        $this->actingAs($admin)->get('/admin/akun')->assertOk();

        $this->actingAs($admin)->get('/siswa/beranda')->assertForbidden();
        $this->actingAs($admin)->get('/guru/beranda')->assertForbidden();
    }

    public function test_halaman_403_ramah_dengan_tombol_kembali_ke_beranda(): void
    {
        $siswa = User::factory()->create(['role' => Role::Siswa]);

        $this->actingAs($siswa)->get('/admin/akun')
            ->assertForbidden()
            ->assertSee('Kamu tidak punya akses ke halaman ini.')
            ->assertSee('Kembali ke Beranda')
            ->assertSee(route('siswa.beranda'));
    }

    public function test_403_dari_kelas_orang_lain_juga_ramah(): void
    {
        $guru = User::factory()->create(['role' => Role::Guru]);
        $kelas = \App\Models\Classroom::factory()->create();

        $this->actingAs($guru)->get("/kelas/{$kelas->id}")
            ->assertForbidden()
            ->assertSee('Kamu tidak punya akses ke halaman ini.')
            ->assertDontSee('This action is unauthorized')
            ->assertSee(route('guru.beranda'));
    }

    public function test_halaman_404_ramah(): void
    {
        $siswa = User::factory()->create(['role' => Role::Siswa]);

        $this->actingAs($siswa)->get('/kelas/99999')
            ->assertNotFound()
            ->assertSee('Halaman tidak ditemukan.')
            ->assertSee('Kembali ke Beranda');

        auth()->logout();
        $this->get('/tidak-ada')->assertNotFound()->assertSee('Kembali ke Login');
    }
}
