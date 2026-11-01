<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_halaman_login_bisa_dibuka(): void
    {
        $this->get('/login')->assertOk()->assertSee('Masuk');
    }

    public function test_siswa_login_masuk_ke_beranda_siswa(): void
    {
        User::factory()->create(['username' => '0012345601', 'role' => Role::Siswa]);

        $this->post('/login', ['username' => '0012345601', 'password' => 'password'])
            ->assertRedirect(route('siswa.beranda'));

        $this->assertAuthenticated();
    }

    public function test_guru_login_masuk_ke_beranda_guru(): void
    {
        User::factory()->create(['username' => 'guru1', 'role' => Role::Guru]);

        $this->post('/login', ['username' => 'guru1', 'password' => 'password'])
            ->assertRedirect(route('guru.beranda'));
    }

    public function test_admin_login_masuk_ke_beranda_admin(): void
    {
        User::factory()->create(['username' => 'admin', 'role' => Role::Admin]);

        $this->post('/login', ['username' => 'admin', 'password' => 'password'])
            ->assertRedirect(route('admin.beranda'));
    }

    public function test_kata_sandi_salah_muncul_pesan(): void
    {
        User::factory()->create(['username' => '0012345601']);

        $this->from('/login')
            ->post('/login', ['username' => '0012345601', 'password' => 'salah'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['username' => 'NISN/username atau kata sandi salah.']);

        $this->assertGuest();
    }

    public function test_akun_nonaktif_tidak_bisa_login(): void
    {
        User::factory()->create(['username' => '0012345601', 'is_active' => false]);

        $this->post('/login', ['username' => '0012345601', 'password' => 'password'])
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_diblokir_sementara_setelah_5_kali_salah(): void
    {
        User::factory()->create(['username' => '0012345601']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['username' => '0012345601', 'password' => 'salah']);
        }

        $this->post('/login', ['username' => '0012345601', 'password' => 'password'])
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_beranda_harus_login(): void
    {
        $this->get('/siswa/beranda')->assertRedirect(route('login'));
        $this->get('/guru/beranda')->assertRedirect(route('login'));
    }

    public function test_sudah_login_buka_login_diarahkan_ke_beranda(): void
    {
        $guru = User::factory()->create(['role' => Role::Guru]);

        $this->actingAs($guru)->get('/login')->assertRedirect(route('guru.beranda'));
    }
}