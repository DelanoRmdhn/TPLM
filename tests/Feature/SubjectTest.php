<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Classroom;
use App\Models\Rombel;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->admin = User::factory()->create(['username' => 'admin', 'role' => Role::Admin]);
    }

    public function test_isi_awal_sembilan_mapel_sd(): void
    {
        $this->assertSame([
            'Pendidikan Agama', 'Pendidikan Pancasila', 'Bahasa Indonesia', 'Matematika', 'IPAS',
            'PJOK', 'Seni dan Budaya', 'Bahasa Inggris', 'Muatan Lokal',
        ], Subject::names()->all());

        $this->actingAs($this->admin)->get('/admin/mapel')
            ->assertOk()
            ->assertSee('Daftar mata pelajaran (9)')
            ->assertSee('Muatan Lokal');
    }

    public function test_admin_menambah_mapel(): void
    {
        $this->actingAs($this->admin)->post('/admin/mapel', ['name' => 'Bahasa Sunda'])
            ->assertRedirect('/admin/mapel')
            ->assertSessionHas('status', 'Mata pelajaran Bahasa Sunda ditambahkan.');

        $this->assertDatabaseHas('subjects', ['name' => 'Bahasa Sunda']);
    }

    public function test_mapel_kosong_atau_dobel_ditolak(): void
    {
        $this->actingAs($this->admin)->post('/admin/mapel', ['name' => ''])
            ->assertSessionHasErrors(['name' => 'Nama mata pelajaran wajib diisi.']);

        $this->actingAs($this->admin)->post('/admin/mapel', ['name' => 'Matematika'])
            ->assertSessionHasErrors(['name' => 'Mata pelajaran ini sudah ada.']);

        $this->assertSame(9, Subject::count());
    }

    public function test_ganti_nama_mapel_ikut_mengganti_kelas(): void
    {
        $ipas = Subject::where('name', 'IPAS')->first();
        $kelas = Classroom::factory()->create(['subject' => 'IPAS']);
        $lain = Classroom::factory()->create(['subject' => 'Matematika']);

        $this->actingAs($this->admin)->put("/admin/mapel/{$ipas->id}", ['name' => 'IPA dan IPS'])
            ->assertRedirect('/admin/mapel');

        $this->assertSame('IPA dan IPS', $ipas->fresh()->name);
        $this->assertSame('IPA dan IPS', $kelas->fresh()->subject);
        $this->assertSame('Matematika', $lain->fresh()->subject);
    }

    public function test_simpan_tanpa_ganti_nama_tidak_dianggap_dobel(): void
    {
        $mtk = Subject::where('name', 'Matematika')->first();

        $this->actingAs($this->admin)->put("/admin/mapel/{$mtk->id}", ['name' => 'Matematika'])
            ->assertSessionHasNoErrors();
    }

    public function test_hapus_mapel_yang_belum_dipakai(): void
    {
        $pjok = Subject::where('name', 'PJOK')->first();

        $this->actingAs($this->admin)->delete("/admin/mapel/{$pjok->id}")
            ->assertSessionHas('status', 'Mata pelajaran PJOK dihapus.');

        $this->assertDatabaseMissing('subjects', ['name' => 'PJOK']);
    }

    public function test_mapel_yang_dipakai_kelas_tidak_bisa_dihapus(): void
    {
        $mtk = Subject::where('name', 'Matematika')->first();
        Classroom::factory()->create(['subject' => 'Matematika']);

        $this->actingAs($this->admin)->delete("/admin/mapel/{$mtk->id}")
            ->assertSessionHasErrors(['name' => 'Matematika dipakai di 1 kelas, jadi tidak bisa dihapus. Ubah namanya saja kalau perlu.']);

        $this->assertDatabaseHas('subjects', ['name' => 'Matematika']);
    }

    public function test_guru_dan_siswa_tidak_bisa_kelola_mapel(): void
    {
        foreach ([Role::Guru, Role::Siswa] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)->get('/admin/mapel')->assertForbidden();
            $this->actingAs($user)->post('/admin/mapel', ['name' => 'Coba'])->assertForbidden();
        }

        $this->assertDatabaseMissing('subjects', ['name' => 'Coba']);
    }

    public function test_form_buat_kelas_memakai_daftar_dari_admin(): void
    {
        $guru = User::factory()->create(['role' => Role::Guru]);
        Subject::create(['name' => 'Bahasa Sunda']);

        $this->actingAs($guru)->get('/guru/buat-kelas')
            ->assertOk()
            ->assertSee('Bahasa Sunda')
            ->assertSee('Muatan Lokal');
    }

    public function test_buat_kelas_dengan_mapel_di_luar_daftar_ditolak(): void
    {
        $guru = User::factory()->create(['role' => Role::Guru]);
        $rombel = Rombel::findOrCreateByName('4A');

        $this->actingAs($guru)->post('/guru/kelas', [
            'nama_kelas' => 'Kimia 4A',
            'mata_pelajaran' => 'Kimia',
            'rombel' => $rombel->id,
            'tahun_ajaran' => Classroom::academicYearOptions()[0],
        ])->assertSessionHasErrors(['mata_pelajaran' => 'Mata pelajaran tidak ada di daftar.']);

        $this->assertSame(0, Classroom::count());
    }
}
