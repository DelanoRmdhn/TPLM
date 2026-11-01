<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MustChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function siswaBaru(): User
    {
        return User::factory()->create([
            'username' => '0012345602',
            'role' => Role::Siswa,
            'must_change_password' => true,
        ]);
    }

    public function test_login_pertama_diarahkan_ke_buat_kata_sandi(): void
    {
        $this->siswaBaru();

        $this->post('/login', ['username' => '0012345602', 'password' => 'password'])
            ->assertRedirect(route('siswa.beranda'));

        // Beranda menolak dan mengarahkan ke halaman buat kata sandi
        $this->get('/siswa/beranda')->assertRedirect(route('password.create'));
        $this->get('/kata-sandi/baru')->assertOk()->assertSee('Buat Kata Sandi Baru');
    }

    public function test_ganti_kata_sandi_berhasil_lalu_bisa_ke_beranda(): void
    {
        $user = $this->siswaBaru();

        $this->actingAs($user)
            ->post('/kata-sandi/baru', [
                'password' => 'sandibaru123',
                'password_confirmation' => 'sandibaru123',
            ])
            ->assertRedirect(route('siswa.beranda'));

        $user->refresh();
        $this->assertFalse($user->must_change_password);
        $this->assertTrue(Hash::check('sandibaru123', $user->password));

        $this->actingAs($user)->get('/siswa/beranda')->assertOk();
    }

    public function test_kata_sandi_terlalu_pendek_ditolak(): void
    {
        $user = $this->siswaBaru();

        $this->actingAs($user)
            ->post('/kata-sandi/baru', ['password' => '123', 'password_confirmation' => '123'])
            ->assertSessionHasErrors(['password' => 'Kata sandi minimal 8 karakter.']);

        $this->assertTrue($user->refresh()->must_change_password);
    }

    public function test_ulangi_kata_sandi_harus_sama(): void
    {
        $user = $this->siswaBaru();

        $this->actingAs($user)
            ->post('/kata-sandi/baru', ['password' => 'sandibaru123', 'password_confirmation' => 'beda12345'])
            ->assertSessionHasErrors(['password' => 'Ulangi kata sandi tidak sama.']);
    }

    public function test_tidak_boleh_sama_dengan_kata_sandi_lama(): void
    {
        $user = $this->siswaBaru();

        $this->actingAs($user)
            ->post('/kata-sandi/baru', ['password' => 'password', 'password_confirmation' => 'password'])
            ->assertSessionHasErrors('password');

        $this->assertTrue($user->refresh()->must_change_password);
    }

    public function test_akun_biasa_tidak_perlu_ke_halaman_ini(): void
    {
        $user = User::factory()->create(['role' => Role::Guru]);

        $this->actingAs($user)->get('/kata-sandi/baru')->assertRedirect(route('guru.beranda'));
        $this->actingAs($user)->get('/guru/beranda')->assertOk();
    }

    public function test_masih_bisa_logout(): void
    {
        $user = $this->siswaBaru();

        $this->actingAs($user)->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }
}