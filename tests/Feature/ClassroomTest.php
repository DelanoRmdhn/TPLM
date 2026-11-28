<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Rombel;
use App\Models\Subject;
use App\Models\Submission;
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
        $this->rombel = Rombel::findOrCreateByName('4A');
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
            ->assertSee('4A (2 siswa)')
            ->assertSee(Classroom::academicYearOptions()[0]);
    }

    public function test_buat_kelas_dan_siswa_aktif_rombel_otomatis_masuk(): void
    {
        $ani = $this->siswa();
        $budi = $this->siswa();
        $nonaktif = $this->siswa(attrs: ['is_active' => false]);
        $lain = $this->siswa(Rombel::findOrCreateByName('4B'));

        $this->actingAs($this->guru)->post('/guru/kelas', [
            'nama_kelas' => 'Matematika 4A',
            'mata_pelajaran' => Subject::where('name', 'Matematika')->value('id'),
            'rombel' => $this->rombel->id,
            'tahun_ajaran' => Classroom::academicYearOptions()[0],
        ])->assertRedirect('/guru/beranda');

        $kelas = Classroom::first();
        $this->assertSame($this->guru->id, $kelas->teacher_id);
        $this->assertSame('Matematika', $kelas->subject->name);
        $this->assertTrue($kelas->hasStudent($ani));
        $this->assertTrue($kelas->hasStudent($budi));
        $this->assertFalse($kelas->hasStudent($nonaktif));
        $this->assertFalse($kelas->hasStudent($lain));

        // Langsung muncul di beranda guru (PB-06)
        $this->actingAs($this->guru)->get('/guru/beranda')
            ->assertSee('Kelas Matematika 4A dibuat. 2 siswa rombel 4A sudah masuk.')
            ->assertSee('Matematika 4A')
            ->assertSee('2 siswa');
    }

    public function test_buat_kelas_ditolak_kalau_data_tidak_valid(): void
    {
        $this->actingAs($this->guru)->post('/guru/kelas', [
            'nama_kelas' => '',
            'mata_pelajaran' => Subject::where('name', 'Matematika')->value('id'),
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
            ->assertSee('Matematika 4A')
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
            ->assertSee('Matematika 4A')
            ->assertSee('Bu Siti')
            ->assertSee('Kelas 4A')
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
        $siswaLuar = $this->siswa(Rombel::findOrCreateByName('4B'));
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

        $rombelB = Rombel::findOrCreateByName('4B');
        $this->siswa($rombelB);
        $this->siswa($rombelB);

        $this->actingAs($this->guru)
            ->post("/kelas/{$kelas->id}/anggota/rombel", ['rombel' => $rombelB->id])
            ->assertSessionHas('status', '2 siswa rombel 4B ditambahkan.');

        $this->actingAs($this->guru)
            ->post("/kelas/{$kelas->id}/anggota/rombel", ['rombel' => $rombelB->id])
            ->assertSessionHas('status', 'Semua siswa rombel 4B sudah ada di kelas ini.');

        $this->assertSame(2, $kelas->students()->count());

        // Siswa 4A yang baru diimpor ikut masuk saat rombel 4A ditambah lagi
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

    // ---------- Ubah, arsip, hapus kelas ----------

    public function test_guru_ubah_nama_mapel_dan_tahun_ajaran_kelas(): void
    {
        $kelas = $this->kelas();
        $ipas = Subject::where('name', 'IPAS')->first();
        $tahun = Classroom::academicYearOptions()[1];

        $this->actingAs($this->guru)->get("/kelas/{$kelas->id}/ubah")->assertOk()->assertSee('Ubah Kelas');

        $this->actingAs($this->guru)->put("/kelas/{$kelas->id}", [
            'nama_kelas' => 'IPAS 4A',
            'mata_pelajaran' => $ipas->id,
            'tahun_ajaran' => $tahun,
        ])->assertRedirect("/kelas/{$kelas->id}");

        $kelas->refresh();
        $this->assertSame('IPAS 4A', $kelas->name);
        $this->assertSame($ipas->id, $kelas->subject_id);
        $this->assertSame($tahun, $kelas->academic_year);
    }

    public function test_ubah_kelas_boleh_tetap_memakai_tahun_ajaran_lama(): void
    {
        $kelas = Classroom::factory()->create(['teacher_id' => $this->guru->id, 'academic_year' => '2020/2021']);

        $this->actingAs($this->guru)->put("/kelas/{$kelas->id}", [
            'nama_kelas' => 'Matematika Lama',
            'mata_pelajaran' => $kelas->subject_id,
            'tahun_ajaran' => '2020/2021',
        ])->assertSessionHasNoErrors();

        $this->actingAs($this->guru)->put("/kelas/{$kelas->id}", [
            'nama_kelas' => 'Matematika Lama',
            'mata_pelajaran' => $kelas->subject_id,
            'tahun_ajaran' => '2019/2020',
        ])->assertSessionHasErrors(['tahun_ajaran' => 'Tahun ajaran tidak valid.']);
    }

    public function test_guru_lain_dan_siswa_tidak_bisa_ubah_arsip_atau_hapus_kelas(): void
    {
        $kelas = $this->kelas();
        $ani = $this->siswa();
        $kelas->addRombel($this->rombel);
        $guruLain = User::factory()->create(['role' => Role::Guru]);

        foreach ([$guruLain, $ani] as $user) {
            $this->actingAs($user)->get("/kelas/{$kelas->id}/ubah")->assertForbidden();
            $this->actingAs($user)->put("/kelas/{$kelas->id}", ['nama_kelas' => 'X'])->assertForbidden();
            $this->actingAs($user)->post("/kelas/{$kelas->id}/arsip")->assertForbidden();
            $this->actingAs($user)->delete("/kelas/{$kelas->id}")->assertForbidden();
        }

        $this->assertSame('Matematika 4A', $kelas->fresh()->name);
        $this->assertFalse($kelas->fresh()->isArchived());
    }

    public function test_arsip_menyembunyikan_kelas_dan_hanya_bisa_dilihat_lalu_dipulihkan(): void
    {
        $kelas = $this->kelas();
        $ani = $this->siswa();
        $kelas->addRombel($this->rombel);
        Assignment::factory()->create(['classroom_id' => $kelas->id, 'title' => 'Latihan Pecahan', 'due_at' => now()->addDay()]);

        $this->actingAs($this->guru)->post("/kelas/{$kelas->id}/arsip")
            ->assertRedirect('/guru/beranda');
        $this->assertTrue($kelas->fresh()->isArchived());

        // Beranda guru: pindah ke bagian arsip
        $this->actingAs($this->guru)->get('/guru/beranda')->assertSee('Kelas diarsipkan (1)');

        // Siswa: hilang dari beranda dan Tugas Saya
        $this->actingAs($ani)->get('/siswa/beranda')->assertDontSee('Matematika 4A');
        $this->actingAs($ani)->get('/siswa/tugas')->assertDontSee('Latihan Pecahan');

        // Hanya bisa dilihat: tidak bisa buat topik atau ubah kelas
        $this->actingAs($this->guru)->get("/kelas/{$kelas->id}")->assertOk()->assertSee('Pulihkan Kelas')->assertDontSee(route('kelas.tugas.create', $kelas), false);
        $this->actingAs($this->guru)->post("/kelas/{$kelas->id}/topik", ['name' => 'Bab 9'])->assertForbidden();
        $this->actingAs($this->guru)->get("/kelas/{$kelas->id}/ubah")->assertForbidden();

        $this->actingAs($this->guru)->post("/kelas/{$kelas->id}/pulihkan")
            ->assertRedirect("/kelas/{$kelas->id}");
        $this->assertFalse($kelas->fresh()->isArchived());
        $this->actingAs($ani)->get('/siswa/beranda')->assertSee('Matematika 4A');
    }

    public function test_hapus_kelas_yang_belum_ada_pengumpulan(): void
    {
        $kelas = $this->kelas();
        $kelas->addRombel($this->rombel);
        Assignment::factory()->create(['classroom_id' => $kelas->id]);
        $kelas->materials()->create(['author_id' => $this->guru->id, 'title' => 'Materi']);

        $this->actingAs($this->guru)->delete("/kelas/{$kelas->id}")
            ->assertRedirect('/guru/beranda')
            ->assertSessionHas('status', 'Kelas Matematika 4A dihapus.');

        $this->assertModelMissing($kelas);
        $this->assertSame(0, Assignment::count());
        $this->assertDatabaseCount('materials', 0);
    }

    public function test_kelas_yang_sudah_ada_pengumpulan_tidak_bisa_dihapus(): void
    {
        $kelas = $this->kelas();
        $ani = $this->siswa();
        $kelas->addRombel($this->rombel);
        $tugas = Assignment::factory()->create(['classroom_id' => $kelas->id]);
        Submission::create(['assignment_id' => $tugas->id, 'student_id' => $ani->id, 'submitted_at' => now()]);

        $this->actingAs($this->guru)->from("/kelas/{$kelas->id}/ubah")->delete("/kelas/{$kelas->id}")
            ->assertSessionHasErrors(['kelas' => 'Kelas ini sudah punya pengumpulan atau nilai siswa, jadi tidak bisa dihapus. Arsipkan saja.']);

        $this->assertModelExists($kelas);
    }

    // ---------- Anggota satuan ----------

    public function test_guru_tambah_satu_siswa_lewat_nisn(): void
    {
        $kelas = $this->kelas();
        $pindahan = $this->siswa(Rombel::findOrCreateByName('4B'), ['name' => 'Citra', 'username' => '0099887766']);

        $this->actingAs($this->guru)->post("/kelas/{$kelas->id}/anggota", ['nisn' => '0099887766'])
            ->assertSessionHas('status', 'Citra ditambahkan ke kelas.');
        $this->assertTrue($kelas->hasStudent($pindahan));

        // Tidak dobel
        $this->actingAs($this->guru)->post("/kelas/{$kelas->id}/anggota", ['nisn' => '0099887766'])
            ->assertSessionHas('status', 'Citra sudah ada di kelas ini.');
        $this->assertSame(1, $kelas->students()->count());
    }

    public function test_tambah_siswa_ditolak_untuk_nisn_guru_atau_siswa_nonaktif(): void
    {
        $kelas = $this->kelas();
        $this->siswa(attrs: ['username' => '0011', 'is_active' => false]);
        User::factory()->create(['role' => Role::Guru, 'username' => 'gurulain']);

        foreach (['0011', 'gurulain', 'tidakada'] as $nisn) {
            $this->actingAs($this->guru)->post("/kelas/{$kelas->id}/anggota", ['nisn' => $nisn])
                ->assertSessionHasErrors(['nisn' => 'Tidak ada siswa aktif dengan NISN ini.']);
        }

        $this->assertSame(0, $kelas->students()->count());
    }

    public function test_guru_keluarkan_siswa_dan_nilainya_tetap_tersimpan(): void
    {
        $kelas = $this->kelas();
        $ani = $this->siswa(attrs: ['name' => 'Ani']);
        $kelas->addRombel($this->rombel);
        $tugas = Assignment::factory()->create(['classroom_id' => $kelas->id]);
        Grade::create(['assignment_id' => $tugas->id, 'student_id' => $ani->id, 'score' => 80]);

        $this->actingAs($this->guru)->delete("/kelas/{$kelas->id}/anggota/{$ani->id}")
            ->assertSessionHas('status', 'Ani dikeluarkan dari kelas.');

        $this->assertFalse($kelas->hasStudent($ani));
        $this->assertSame(1, Grade::count());
        $this->actingAs($ani)->get("/kelas/{$kelas->id}")->assertForbidden();
    }

    public function test_keluarkan_siswa_yang_bukan_anggota_kelas_ini_tidak_ditemukan(): void
    {
        $kelas = $this->kelas();
        $lain = $this->siswa();

        $this->actingAs($this->guru)->delete("/kelas/{$kelas->id}/anggota/{$lain->id}")->assertNotFound();
    }

    public function test_siswa_tidak_bisa_tambah_atau_keluarkan_anggota(): void
    {
        $kelas = $this->kelas();
        $ani = $this->siswa();
        $budi = $this->siswa(attrs: ['username' => '0055']);
        $kelas->addRombel($this->rombel);

        $this->actingAs($ani)->post("/kelas/{$kelas->id}/anggota", ['nisn' => '0055'])->assertForbidden();
        $this->actingAs($ani)->delete("/kelas/{$kelas->id}/anggota/{$budi->id}")->assertForbidden();
        $this->assertTrue($kelas->hasStudent($budi));
    }
}
