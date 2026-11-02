<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\RegistrationSetting;
use App\Models\Rombel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function open(): void
    {
        RegistrationSetting::current()->update(['is_open' => true]);
    }

    public function test_tombol_daftar_hanya_muncul_saat_dibuka(): void
    {
        $this->get('/login')->assertDontSee('Daftar Akun Baru');

        $this->open();
        $this->get('/login')->assertSee('Daftar Akun Baru');
    }

    public function test_form_daftar_tidak_bisa_dibuka_saat_ditutup(): void
    {
        $this->get('/daftar')->assertRedirect('/login');
        $this->post('/daftar', [])->assertRedirect('/login');

        $this->get('/login')->assertSee('Pendaftaran akun sedang ditutup.');
    }

    public function test_siswa_daftar_dan_akun_langsung_aktif(): void
    {
        $this->open();
        $rombel = Rombel::findOrCreateByName('4-A');

        $this->get('/daftar')->assertOk()->assertSee('4-A');

        $this->post('/daftar', [
            'role' => 'siswa',
            'name' => 'Ani',
            'username' => '0012345601',
            'rombel_id' => $rombel->id,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect('/daftar/berhasil');

        $this->get('/daftar/berhasil')->assertOk()->assertSee('0012345601');

        $ani = User::where('username', '0012345601')->first();
        $this->assertSame(Role::Siswa, $ani->role);
        $this->assertSame($rombel->id, $ani->rombel_id);
        $this->assertTrue($ani->is_active);
        $this->assertFalse($ani->must_change_password);

        $this->post('/login', ['username' => '0012345601', 'password' => 'rahasia123'])
            ->assertRedirect(route('siswa.beranda'));
    }

    public function test_guru_daftar_tanpa_rombel(): void
    {
        $this->open();

        $this->post('/daftar', [
            'role' => 'guru',
            'name' => 'Pak Joko',
            'username' => '198501012010011002',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect('/daftar/berhasil');

        $guru = User::where('username', '198501012010011002')->first();
        $this->assertSame(Role::Guru, $guru->role);
        $this->assertNull($guru->rombel_id);
    }

    public function test_guru_daftar_menunggu_persetujuan_admin(): void
    {
        $this->open();

        $this->post('/daftar', [
            'role' => 'guru',
            'name' => 'Pak Joko',
            'username' => 'pakjoko',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect('/daftar/berhasil');

        $guru = User::where('username', 'pakjoko')->first();
        $this->assertFalse($guru->is_active);

        $this->get('/daftar/berhasil')
            ->assertSee('Akun kamu menunggu persetujuan admin sekolah.')
            ->assertDontSee('Akun kamu sudah aktif.');

        // Belum bisa login sebelum diaktifkan admin
        $this->post('/login', ['username' => 'pakjoko', 'password' => 'rahasia123'])
            ->assertSessionHasErrors(['username' => 'Akun kamu tidak aktif. Hubungi admin sekolah.']);
        $this->assertGuest();

        // Admin mengaktifkan di Kelola akun, lalu bisa login
        $admin = User::factory()->create(['username' => 'admin', 'role' => Role::Admin]);
        $this->actingAs($admin)->post("/admin/akun/{$guru->id}/aktifkan");
        auth()->logout();

        $this->post('/login', ['username' => 'pakjoko', 'password' => 'rahasia123'])
            ->assertRedirect('/guru/beranda');
    }

    public function test_nisn_yang_sudah_terdaftar_ditolak(): void
    {
        $this->open();
        $rombel = Rombel::findOrCreateByName('4-A');
        User::factory()->create(['username' => '0012345601']);

        $this->post('/daftar', [
            'role' => 'siswa',
            'name' => 'Ani',
            'username' => '0012345601',
            'rombel_id' => $rombel->id,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertSessionHasErrors('username');

        $this->assertSame(1, User::count());
    }

    public function test_tidak_bisa_daftar_sebagai_admin(): void
    {
        $this->open();

        $this->post('/daftar', [
            'role' => 'admin',
            'name' => 'Penyusup',
            'username' => 'penyusup',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertSessionHasErrors('role');

        $this->assertSame(0, User::count());
    }

    public function test_halaman_berhasil_tanpa_daftar_diarahkan_ke_login(): void
    {
        $this->get('/daftar/berhasil')->assertRedirect('/login');
    }
}
