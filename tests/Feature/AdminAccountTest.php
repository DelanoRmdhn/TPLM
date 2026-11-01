<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\RegistrationSetting;
use App\Models\Rombel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->admin = User::factory()->create(['username' => 'admin', 'role' => Role::Admin]);
    }

    public function test_daftar_akun_tampil_tanpa_admin(): void
    {
        User::factory()->create(['name' => 'Budi Siswa']);
        User::factory()->create(['name' => 'Ibu Guru', 'role' => Role::Guru]);

        $this->actingAs($this->admin)->get('/admin/akun')
            ->assertOk()
            ->assertSee('Budi Siswa')
            ->assertSee('Ibu Guru')
            ->assertSee('2 akun');
    }

    public function test_filter_dan_cari(): void
    {
        User::factory()->create(['name' => 'Budi Siswa']);
        User::factory()->create(['name' => 'Ibu Guru', 'role' => Role::Guru]);

        $this->actingAs($this->admin)->get('/admin/akun?peran=guru')
            ->assertSee('Ibu Guru')->assertDontSee('Budi Siswa');

        $this->actingAs($this->admin)->get('/admin/akun?q=budi')
            ->assertSee('Budi Siswa')->assertDontSee('Ibu Guru');
    }

    public function test_tambah_akun_siswa_dengan_kata_sandi_awal(): void
    {
        $this->actingAs($this->admin)->post('/admin/akun', [
            'role' => 'siswa',
            'name' => 'Ani',
            'username' => '0012345678',
            'rombel' => '4-b',
        ])->assertRedirect('/admin/akun')->assertSessionHas('temporary_password');

        $ani = User::where('username', '0012345678')->first();
        $this->assertSame(Role::Siswa, $ani->role);
        $this->assertTrue($ani->must_change_password);
        $this->assertSame('4-B', $ani->rombel->name);

        $password = session('temporary_password')['password'];
        $this->assertTrue(Hash::check($password, $ani->password));
    }

    public function test_tambah_akun_ditolak_kalau_nisn_salah_atau_dobel(): void
    {
        User::factory()->create(['username' => '0012345678']);

        $this->actingAs($this->admin)->post('/admin/akun', [
            'role' => 'siswa', 'name' => 'Ani', 'username' => '0012345678', 'rombel' => '4-A',
        ])->assertSessionHasErrors(['username' => 'NISN ini sudah terdaftar.']);

        $this->actingAs($this->admin)->post('/admin/akun', [
            'role' => 'siswa', 'name' => 'Ani', 'username' => '12345', 'rombel' => '4-A',
        ])->assertSessionHasErrors(['username' => 'NISN harus 10 digit angka.']);

        $this->actingAs($this->admin)->post('/admin/akun', [
            'role' => 'siswa', 'name' => 'Ani', 'username' => '0099999999',
        ])->assertSessionHasErrors('rombel');
    }

    public function test_ubah_akun(): void
    {
        $guru = User::factory()->create(['role' => Role::Guru, 'username' => 'guru1']);

        $this->actingAs($this->admin)->put("/admin/akun/{$guru->id}", [
            'role' => 'guru', 'name' => 'Pak Guru Baru', 'username' => 'guru1',
        ])->assertRedirect('/admin/akun');

        $this->assertSame('Pak Guru Baru', $guru->refresh()->name);
    }

    public function test_nonaktifkan_lalu_tidak_bisa_login_dan_data_tetap_ada(): void
    {
        $siswa = User::factory()->create(['username' => '0012345601']);

        $this->actingAs($this->admin)->post("/admin/akun/{$siswa->id}/nonaktifkan")->assertRedirect();
        $this->assertFalse($siswa->refresh()->is_active);
        $this->assertDatabaseHas('users', ['id' => $siswa->id]);

        auth()->logout();
        $this->post('/login', ['username' => '0012345601', 'password' => 'password'])
            ->assertSessionHasErrors('username');
        $this->assertGuest();

        $this->actingAs($this->admin)->post("/admin/akun/{$siswa->id}/aktifkan");
        $this->assertTrue($siswa->refresh()->is_active);
    }

    public function test_reset_kata_sandi(): void
    {
        $siswa = User::factory()->create();

        $this->actingAs($this->admin)->post("/admin/akun/{$siswa->id}/reset-kata-sandi")
            ->assertSessionHas('temporary_password');

        $siswa->refresh();
        $this->assertTrue($siswa->must_change_password);
        $this->assertTrue(Hash::check(session('temporary_password')['password'], $siswa->password));
        $this->assertFalse(Hash::check('password', $siswa->password));
    }

    public function test_akun_admin_tidak_bisa_dikelola(): void
    {
        $this->actingAs($this->admin)->post("/admin/akun/{$this->admin->id}/nonaktifkan")->assertNotFound();
        $this->actingAs($this->admin)->get("/admin/akun/{$this->admin->id}/ubah")->assertNotFound();
    }

    public function test_buka_tutup_pendaftaran(): void
    {
        $this->assertFalse(RegistrationSetting::isOpen());

        $this->actingAs($this->admin)->put('/admin/pendaftaran', ['is_open' => 1]);
        $this->assertTrue(RegistrationSetting::isOpen());

        $this->actingAs($this->admin)->put('/admin/pendaftaran', ['is_open' => 0]);
        $this->assertFalse(RegistrationSetting::isOpen());
    }

    public function test_rombel_huruf_kecil_disamakan(): void
    {
        $this->assertSame(Rombel::findOrCreateByName('4-a')->id, Rombel::findOrCreateByName(' 4-A ')->id);
    }
}
