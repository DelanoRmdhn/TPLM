<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Rombel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_semua_peran_bisa_buka_halaman_akun(): void
    {
        $siswa = User::factory()->create([
            'name' => 'Ani', 'username' => '0012345601', 'role' => Role::Siswa,
            'rombel_id' => Rombel::findOrCreateByName('4-A')->id,
        ]);

        $this->actingAs($siswa)->get('/akun')->assertOk()
            ->assertSee('Ani')->assertSee('NISN')->assertSee('0012345601')->assertSee('4-A');

        foreach ([Role::Guru, Role::Admin] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))->get('/akun')->assertOk()->assertSee('Ganti kata sandi');
        }
    }

    public function test_ganti_kata_sandi_berhasil(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from('/akun')->put('/akun/kata-sandi', [
            'current_password' => 'password',
            'password' => 'sandibaru123',
            'password_confirmation' => 'sandibaru123',
        ])->assertRedirect('/akun')->assertSessionHas('status', 'Kata sandi berhasil diganti.');

        $this->assertTrue(Hash::check('sandibaru123', $user->refresh()->password));

        auth()->logout();
        $this->post('/login', ['username' => $user->username, 'password' => 'sandibaru123'])->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_kata_sandi_lama_salah_ditolak(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/akun/kata-sandi', [
            'current_password' => 'salah',
            'password' => 'sandibaru123',
            'password_confirmation' => 'sandibaru123',
        ])->assertSessionHasErrors(['current_password' => 'Kata sandi lama salah.']);

        $this->assertTrue(Hash::check('password', $user->refresh()->password));
    }

    public function test_validasi_kata_sandi_baru(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/akun/kata-sandi', [
            'current_password' => 'password', 'password' => '123', 'password_confirmation' => '123',
        ])->assertSessionHasErrors(['password' => 'Kata sandi baru minimal 8 karakter.']);

        $this->actingAs($user)->put('/akun/kata-sandi', [
            'current_password' => 'password', 'password' => 'sandibaru123', 'password_confirmation' => 'beda12345',
        ])->assertSessionHasErrors(['password' => 'Ulangi kata sandi baru tidak sama.']);

        $this->actingAs($user)->put('/akun/kata-sandi', [
            'current_password' => 'password', 'password' => 'password', 'password_confirmation' => 'password',
        ])->assertSessionHasErrors(['password' => 'Kata sandi baru tidak boleh sama dengan kata sandi lama.']);
    }

    public function test_perangkat_lain_dikeluarkan_setelah_ganti_kata_sandi(): void
    {
        config(['session.driver' => 'database']);
        $user = User::factory()->create();
        DB::table('sessions')->insert([
            'id' => 'sesi-hp-lain', 'user_id' => $user->id, 'payload' => '', 'last_activity' => time(),
        ]);

        $this->actingAs($user)->put('/akun/kata-sandi', [
            'current_password' => 'password', 'password' => 'sandibaru123', 'password_confirmation' => 'sandibaru123',
        ]);

        $this->assertDatabaseMissing('sessions', ['id' => 'sesi-hp-lain']);
    }

    public function test_harus_login(): void
    {
        $this->get('/akun')->assertRedirect('/login');
    }

    public function test_tombol_akun_di_navigasi_bawah(): void
    {
        $this->actingAs(User::factory()->create())->get('/siswa/beranda')->assertSee(route('akun'), false);
        $this->actingAs(User::factory()->create(['role' => Role::Guru]))->get('/guru/beranda')->assertSee(route('akun'), false);
    }
}
