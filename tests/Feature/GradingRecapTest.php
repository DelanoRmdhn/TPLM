<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Rombel;
use App\Models\Submission;
use App\Models\User;
use App\Support\SimpleXlsx;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradingRecapTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;

    private User $ani;

    private User $budi;

    private Classroom $kelas;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $rombel = Rombel::findOrCreateByName('4A');
        $this->guru = User::factory()->create(['role' => Role::Guru]);
        $this->ani = User::factory()->create(['role' => Role::Siswa, 'name' => 'Ani', 'username' => '0001', 'rombel_id' => $rombel->id]);
        $this->budi = User::factory()->create(['role' => Role::Siswa, 'name' => 'Budi', 'username' => '0002', 'rombel_id' => $rombel->id]);
        $this->kelas = Classroom::factory()->create(['teacher_id' => $this->guru->id, 'name' => 'Matematika 4A', 'rombel_id' => $rombel->id]);
        $this->kelas->addRombel($rombel);
    }

    private function kumpul(Assignment $tugas, User $siswa, ?int $nilai = null): void
    {
        Submission::create(['assignment_id' => $tugas->id, 'student_id' => $siswa->id, 'submitted_at' => now()->subHour()]);

        if ($nilai !== null) {
            Grade::create(['assignment_id' => $tugas->id, 'student_id' => $siswa->id, 'score' => $nilai]);
        }
    }

    // ---------- Penilaian ----------

    public function test_penilaian_menampilkan_pengumpulan_yang_belum_dinilai_per_kelas_dan_tugas(): void
    {
        $tugas = Assignment::factory()->create(['classroom_id' => $this->kelas->id, 'title' => 'Latihan Pecahan']);
        $this->kumpul($tugas, $this->ani);
        $this->kumpul($tugas, $this->budi, nilai: 90);

        $this->actingAs($this->guru)->get('/guru/penilaian')
            ->assertOk()
            ->assertSee('1 tugas siswa menunggu nilai')
            ->assertSeeInOrder(['Matematika 4A', 'Latihan Pecahan', 'Ani'])
            ->assertSee(route('kelas.tugas.nilai.edit', [$this->kelas, $tugas, $this->ani]), false)
            ->assertDontSee('Budi');
    }

    public function test_penilaian_tidak_memuat_kelas_guru_lain_kelas_arsip_atau_siswa_yang_dikeluarkan(): void
    {
        $guruLain = User::factory()->create(['role' => Role::Guru]);
        $kelasLain = Classroom::factory()->create(['teacher_id' => $guruLain->id]);
        $kelasLain->students()->attach($this->ani);
        $this->kumpul(Assignment::factory()->create(['classroom_id' => $kelasLain->id, 'title' => 'Tugas Orang']), $this->ani);

        $arsip = Classroom::factory()->create(['teacher_id' => $this->guru->id, 'archived_at' => now()]);
        $arsip->students()->attach($this->ani);
        $this->kumpul(Assignment::factory()->create(['classroom_id' => $arsip->id, 'title' => 'Tugas Lama']), $this->ani);

        $this->kumpul(Assignment::factory()->create(['classroom_id' => $this->kelas->id, 'title' => 'Tugas Budi']), $this->budi);
        $this->kelas->students()->detach($this->budi);

        $this->actingAs($this->guru)->get('/guru/penilaian')
            ->assertOk()
            ->assertSee('Semua pengumpulan sudah dinilai.')
            ->assertDontSee('Tugas Orang')
            ->assertDontSee('Tugas Lama')
            ->assertDontSee('Tugas Budi');
    }

    public function test_menu_penilaian_dan_rekap_hanya_untuk_guru(): void
    {
        $this->actingAs($this->ani)->get('/guru/penilaian')->assertForbidden();
        $this->actingAs($this->ani)->get('/guru/rekap')->assertForbidden();

        $this->actingAs($this->guru)->get('/guru/beranda')
            ->assertSee(route('guru.penilaian'), false)
            ->assertSee(route('guru.rekap'), false);
    }

    // ---------- Rekap ----------

    public function test_rekap_menampilkan_nilai_status_dan_rata_rata_skala_100(): void
    {
        $tugas1 = Assignment::factory()->create(['classroom_id' => $this->kelas->id, 'title' => 'Tugas 1', 'max_score' => 50, 'due_at' => now()->addDay()]);
        $tugas2 = Assignment::factory()->create(['classroom_id' => $this->kelas->id, 'title' => 'Tugas 2', 'max_score' => 100, 'due_at' => now()->addDays(2)]);
        $this->kumpul($tugas1, $this->ani, nilai: 45); // 90 dalam skala 100
        $this->kumpul($tugas2, $this->ani, nilai: 80);
        $this->kumpul($tugas1, $this->budi);

        $this->actingAs($this->guru)->get('/guru/rekap')
            ->assertOk()
            ->assertSee(route('kelas.rekap', $this->kelas), false);

        $this->actingAs($this->guru)->get("/kelas/{$this->kelas->id}/rekap")
            ->assertOk()
            ->assertSeeInOrder(['Tugas 1', 'Tugas 2', 'Rata-rata'])
            ->assertSeeInOrder(['Ani', '45', '80', '85'])
            ->assertSeeInOrder(['Budi', 'Diserahkan', 'Belum dikerjakan', '–']);
    }

    public function test_unduh_rekap_sebagai_excel(): void
    {
        $tugas = Assignment::factory()->create(['classroom_id' => $this->kelas->id, 'title' => 'Tugas 1', 'max_score' => 50]);
        $this->kumpul($tugas, $this->ani, nilai: 45);

        $response = $this->actingAs($this->guru)->get("/kelas/{$this->kelas->id}/rekap/unduh")->assertOk();
        $this->assertStringContainsString('rekap-nilai-matematika-4a-', $response->headers->get('Content-Disposition'));

        $path = tempnam(sys_get_temp_dir(), 'rekap');
        file_put_contents($path, $response->getContent());

        $this->assertSame([
            1 => ['Nama', 'NISN', 'Tugas 1 (maks 50)', 'Rata-rata (skala 100)'],
            2 => ['Ani', '0001', '45', '90'],
            3 => ['Budi', '0002', 'Belum dikerjakan', ''],
        ], SimpleXlsx::read($path));
    }

    public function test_rekap_hanya_untuk_guru_pengajar_dan_tetap_bisa_untuk_kelas_arsip(): void
    {
        $guruLain = User::factory()->create(['role' => Role::Guru]);

        $this->actingAs($guruLain)->get("/kelas/{$this->kelas->id}/rekap")->assertForbidden();
        $this->actingAs($guruLain)->get("/kelas/{$this->kelas->id}/rekap/unduh")->assertForbidden();
        $this->actingAs($this->ani)->get("/kelas/{$this->kelas->id}/rekap")->assertForbidden();

        $this->kelas->update(['archived_at' => now()]);
        $this->actingAs($this->guru)->get("/kelas/{$this->kelas->id}/rekap")->assertOk();
        $this->actingAs($this->guru)->get("/kelas/{$this->kelas->id}/rekap/unduh")->assertOk();
    }
}
