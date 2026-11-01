<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Classroom;
use App\Models\Rombel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassroomTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;

    private Rombel $rombel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->guru = User::factory()->create(['name' => 'Bu Siti', 'role' => Role::Guru]);
        $this->rombel = Rombel::findOrCreateByName('4-A');
    }

    private function siswa(?Rombel $rombel = null, array $attrs = []): User
    {
        return User::factory()->create(['role' => Role::Siswa, 'rombel_id' => ($rombel ?? $this->rombel)->id, ...$attrs]);
    }

    private function kelas(?User $guru = null): Classroom
    {
        return Classroom::factory()->create(['teacher_id' => ($guru ?? $this->guru)->id, 'rombel_id' => $this->rombel->id]);
    }

    public function test_form_buat_kelas_menampilkan_rombel_dan_jumlah_siswa(): void
    {
        $this->siswa();
        $this->siswa();
        $this->siswa(attrs: ['is_active' => false]);

        $this->actingAs($this->guru)->get('/guru/buat-kelas')
            ->assertOk()
            ->assertSee('4-A · 2 siswa')
            ->assertSee(Classroom::academicYearOptions()[0]);
    }

    public function test_buat_kelas_dan_siswa_aktif_rombel_otomatis_masuk(): void
    {
        $ani = $this->siswa();
        $budi = $this->siswa();
        $nonaktif = $this->siswa(attrs: ['is_active' => false]);
        $lain = $this->siswa(Rombel::findOrCreateByName('4-B'));

        $this->actingAs($this->guru)->post('/guru/kelas', [
            'nama_kelas' => 'Matematika 4-A',
            'mata_pelajaran' => 'Matematika',
            'rombel' => $this->rombel->id,
            'tahun_ajaran' => Classroom::academicYearOptions()[0],
        ])->assertRedirect('/guru/beranda');

        $kelas = Classroom::first();
        $this->assertSame($this->guru->id, $kelas->teacher_id);
        $this->assertTrue($kelas->hasStudent($ani));
        $this->assertTrue($kelas->hasStudent($budi));
        $this->assertFalse($kelas->hasStudent($nonaktif));
        $this->assertFalse($kelas->hasStudent($lain));

        // Langsung muncul di beranda guru (PB-06)
        $this->actingAs($this->guru)->get('/guru/beranda')
            ->assertSee('Kelas Matematika 4-A dibuat. 2 siswa rombel 4-A sudah masuk.')
            ->assertSee('Matematika 4-A')
            ->assertSee('2 siswa');
    }

    public function test_buat_kelas_ditolak_kalau_data_tidak_valid(): void
    {
        $this->actingAs($this->guru)->post('/guru/kelas', [
            'nama_kelas' => '',
            'mata_pelajaran' => 'Matematika',
            'rombel' => 999,
            'tahun_ajaran' => '1990/1991',
        ])->assertSessionHasErrors(['nama_kelas', 'rombel', 'tahun_ajaran']);

        $this->assertSame(0, Classroom::count());
    }

    public function test_beranda_guru_hanya_kelas_sendiri(): void
    {
        $this->kelas();
        $guruLain = User::factory()->create(['role' => Role::Guru]);
        Classroom::factory()->create(['teacher_id' => $guruLain->id, 'name' => 'IPA Punya Orang']);

        $this->actingAs($this->guru)->get('/guru/beranda')
            ->assertSee('Matematika 4-A')
            ->assertDontSee('IPA Punya Orang');
    }

    public function test_beranda_siswa_menampilkan_kelas_dan_guru(): void
    {
        $ani = $this->siswa();
        $kelas = $this->kelas();
        $kelas->addRombel($this->rombel);

        $guruLain = User::factory()->create(['role' => Role::Guru]);
        Classroom::factory()->create(['teacher_id' => $guruLain->id, 'name' => 'IPA Punya Orang']);

        $this->actingAs($ani)->get('/siswa/beranda')
            ->assertOk()
            ->assertSee('Matematika 4-A')
            ->assertSee('Bu Siti')
            ->assertSee('Kelas 4-A')
            ->assertDontSee('IPA Punya Orang');
    }

    public function test_anggota_kelas_bisa_dilihat_guru_dan_siswa_anggota(): void
    {
        $ani = $this->siswa(attrs: ['name' => 'Ani', 'username' => '0012345601']);
        $kelas = $this->kelas();
        $kelas->addRombel($this->rombel);

        $this->actingAs($this->guru)->get("/kelas/{$kelas->id}/anggota")
            ->assertOk()->assertSee('Ani')->assertSee('0012345601')->assertSee('Tambah satu rombel');

        // Siswa lihat nama teman, tanpa NISN dan tanpa tombol tambah rombel
        $this->actingAs($ani)->get("/kelas/{$kelas->id}/anggota")
            ->assertOk()->assertSee('Ani')->assertDontSee('0012345601')->assertDontSee('Tambah satu rombel');
    }

    public function test_kelas_orang_lain_tidak_bisa_dibuka_lewat_url(): void
    {
        $kelas = $this->kelas();
        $guruLain = User::factory()->create(['role' => Role::Guru]);
        $siswaLuar = $this->siswa(Rombel::findOrCreateByName('4-B'));
        $admin = User::factory()->create(['role' => Role::Admin]);

        $this->actingAs($guruLain)->get("/kelas/{$kelas->id}/anggota")->assertForbidden();
        $this->actingAs($siswaLuar)->get("/kelas/{$kelas->id}/anggota")->assertForbidden();
        $this->actingAs($admin)->get("/kelas/{$kelas->id}/anggota")->assertForbidden();
    }

    public function test_tambah_rombel_ke_kelas_tanpa_dobel(): void
    {
        $kelas = $this->kelas();
        $kelas->addRombel($this->rombel);
        $this->siswa();

        $rombelB = Rombel::findOrCreateByName('4-B');
        $this->siswa($rombelB);
        $this->siswa($rombelB);

        $this->actingAs($this->guru)
            ->post("/kelas/{$kelas->id}/anggota/rombel", ['rombel' => $rombelB->id])
            ->assertSessionHas('status', '2 siswa rombel 4-B ditambahkan.');

        $this->actingAs($this->guru)
            ->post("/kelas/{$kelas->id}/anggota/rombel", ['rombel' => $rombelB->id])
            ->assertSessionHas('status', 'Semua siswa rombel 4-B sudah ada di kelas ini.');

        $this->assertSame(2, $kelas->students()->count());

        // Siswa 4-A yang baru diimpor ikut masuk saat rombel 4-A ditambah lagi
        $this->actingAs($this->guru)->post("/kelas/{$kelas->id}/anggota/rombel", ['rombel' => $this->rombel->id]);
        $this->assertSame(3, $kelas->students()->count());
    }

    public function test_hanya_guru_pengajar_yang_bisa_tambah_rombel(): void
    {
        $kelas = $this->kelas();
        $guruLain = User::factory()->create(['role' => Role::Guru]);
        $ani = $this->siswa();
        $kelas->addRombel($this->rombel);

        $this->actingAs($guruLain)
            ->post("/kelas/{$kelas->id}/anggota/rombel", ['rombel' => $this->rombel->id])
            ->assertForbidden();

        $this->actingAs($ani)
            ->post("/kelas/{$kelas->id}/anggota/rombel", ['rombel' => $this->rombel->id])
            ->assertForbidden();
    }
}
