<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Classroom;
use App\Models\Rombel;
use App\Models\User;
use App\Support\SimpleXlsx;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RombelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->admin = User::factory()->create(['username' => 'admin', 'role' => Role::Admin]);
    }

    public function test_isi_awal_1a_sampai_6b(): void
    {
        $this->assertSame(
            ['1A', '1B', '2A', '2B', '3A', '3B', '4A', '4B', '5A', '5B', '6A', '6B'],
            Rombel::orderBy('name')->pluck('name')->all(),
        );
        $this->assertSame(4, Rombel::findByName('4A')->grade_level);

        $this->actingAs($this->admin)->get('/admin/rombel')
            ->assertOk()
            ->assertSee('Daftar rombel (12)');
    }

    public function test_nama_rombel_disamakan_jadi_satu_format(): void
    {
        foreach (['4-a', ' 4 A ', '4.A', '4A'] as $name) {
            $this->assertSame('4A', Rombel::normalizeName($name));
        }

        $this->actingAs($this->admin)->post('/admin/rombel', ['name' => '4-c'])
            ->assertSessionHas('status', 'Rombel 4C ditambahkan.');
        $this->assertSame(4, Rombel::findByName('4C')->grade_level);

        // "4-A" sama dengan 4A yang sudah ada
        $this->actingAs($this->admin)->post('/admin/rombel', ['name' => '4-A'])
            ->assertSessionHasErrors(['name' => 'Rombel ini sudah ada.']);
    }

    public function test_format_rombel_salah_ditolak(): void
    {
        foreach (['', 'A4', '7A', 'Kelas Empat'] as $name) {
            $this->actingAs($this->admin)->post('/admin/rombel', ['name' => $name])->assertSessionHasErrors('name');
        }

        $this->assertSame(12, Rombel::count());
    }

    public function test_ganti_nama_rombel_siswa_dan_kelas_ikut(): void
    {
        $rombel = Rombel::findByName('4A');
        $siswa = User::factory()->create(['rombel_id' => $rombel->id]);
        $kelas = Classroom::factory()->create(['rombel_id' => $rombel->id]);

        $this->actingAs($this->admin)->put("/admin/rombel/{$rombel->id}", ['name' => '4-D'])
            ->assertSessionHas('status', 'Rombel 4A diubah jadi 4D.');

        $this->assertSame('4D', $siswa->fresh()->rombel->name);
        $this->assertSame('4D', $kelas->fresh()->rombel->name);
    }

    public function test_rombel_yang_masih_dipakai_tidak_bisa_dihapus(): void
    {
        $rombel = Rombel::findByName('4A');
        User::factory()->create(['rombel_id' => $rombel->id]);

        $this->actingAs($this->admin)->delete("/admin/rombel/{$rombel->id}")
            ->assertSessionHasErrors(['name' => 'Rombel 4A masih dipakai 1 siswa, jadi tidak bisa dihapus.']);
        $this->assertNotNull($rombel->fresh());

        $kosong = Rombel::findByName('6B');
        $this->actingAs($this->admin)->delete("/admin/rombel/{$kosong->id}")
            ->assertSessionHas('status', 'Rombel 6B dihapus.');
        $this->assertNull(Rombel::findByName('6B'));
    }

    public function test_guru_dan_siswa_tidak_bisa_kelola_rombel(): void
    {
        foreach ([Role::Guru, Role::Siswa] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get('/admin/rombel')->assertForbidden();
            $this->actingAs($user)->post('/admin/rombel', ['name' => '4C'])->assertForbidden();
        }

        $this->assertNull(Rombel::findByName('4C'));
    }

    public function test_impor_dengan_rombel_di_luar_daftar_dilaporkan(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'test').'.xlsx';
        file_put_contents($path, SimpleXlsx::write([
            ['Nama', 'NISN', 'Rombel'],
            ['Ani', '0012345601', '4-a'],
            ['Budi', '0012345602', '4D'],
        ]));

        $this->actingAs($this->admin)->post('/admin/impor', [
            'type' => 'siswa', 'file' => new UploadedFile($path, 'data.xlsx', null, null, true),
        ]);

        $this->actingAs($this->admin)->get('/admin/impor/cek')
            ->assertSee('Rombel 4D tidak ada di daftar. Tambahkan dulu di menu Rombel.')
            ->assertSee('Simpan 1 Akun');

        $this->actingAs($this->admin)->post('/admin/impor/simpan');

        $this->assertSame('4A', User::where('username', '0012345601')->first()->rombel->name);
        $this->assertNull(Rombel::findByName('4D'));
    }

    public function test_tambah_akun_dengan_rombel_di_luar_daftar_ditolak(): void
    {
        $this->actingAs($this->admin)->post('/admin/akun', [
            'role' => 'siswa', 'name' => 'Ani', 'username' => '0012345601', 'rombel' => '4D',
        ])->assertSessionHasErrors(['rombel' => 'Rombel tidak ada di daftar. Tambahkan dulu di menu Rombel.']);

        $this->assertNull(User::where('username', '0012345601')->first());
        $this->assertNull(Rombel::findByName('4D'));
    }

    public function test_migration_mengganti_format_lama_dan_menggabung_yang_dobel(): void
    {
        // Data lokal lama: "4-A" (dobel dengan 4A) dan "4-C" (belum ada)
        $lamaA = DB::table('rombels')->insertGetId(['name' => '4-A', 'grade_level' => 4]);
        $lamaC = DB::table('rombels')->insertGetId(['name' => '4-C', 'grade_level' => 4]);
        $ani = User::factory()->create(['rombel_id' => $lamaA]);
        $budi = User::factory()->create(['rombel_id' => $lamaC]);
        $kelas = Classroom::factory()->create(['rombel_id' => $lamaA]);

        (include database_path('migrations/2026_11_02_000002_normalize_and_fill_rombels.php'))->up();

        $this->assertNull(DB::table('rombels')->where('name', '4-A')->first());
        $this->assertSame('4A', $ani->fresh()->rombel->name);
        $this->assertSame('4A', $kelas->fresh()->rombel->name);
        $this->assertSame('4C', $budi->fresh()->rombel->name);
        $this->assertSame(13, Rombel::count());
    }
}
