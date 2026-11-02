<?php

namespace Tests\Feature;

use App\Enums\AssignmentStatus;
use App\Enums\Role;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Rombel;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssignmentTest extends TestCase
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
        Storage::fake('local');

        $rombel = Rombel::findOrCreateByName('4-A');
        $this->guru = User::factory()->create(['role' => Role::Guru]);
        $this->ani = User::factory()->create(['role' => Role::Siswa, 'name' => 'Ani', 'rombel_id' => $rombel->id]);
        $this->budi = User::factory()->create(['role' => Role::Siswa, 'name' => 'Budi', 'rombel_id' => $rombel->id]);
        $this->kelas = Classroom::factory()->create(['teacher_id' => $this->guru->id, 'rombel_id' => $rombel->id]);
        $this->kelas->addRombel($rombel);
    }

    private function tugas(array $attrs = []): Assignment
    {
        return Assignment::factory()->create(['classroom_id' => $this->kelas->id, ...$attrs]);
    }

    private function url(Assignment $tugas, string $suffix = ''): string
    {
        return "/kelas/{$this->kelas->id}/tugas/{$tugas->id}{$suffix}";
    }

    // ---------- Hal 5: buat tugas ----------

    public function test_guru_buat_tugas_dan_muncul_di_kelas_siswa(): void
    {
        $due = now()->addDays(2)->setTime(23, 59);

        $this->actingAs($this->guru)->post("/kelas/{$this->kelas->id}/tugas", [
            'title' => 'Latihan Pecahan',
            'instructions' => 'Kerjakan halaman 12.',
            'due_at' => $due->format('Y-m-d\TH:i'),
            'max_score' => 100,
            'files' => [UploadedFile::fake()->createWithContent('soal.pdf', "%PDF-1.4\n%%EOF")],
            'links' => 'https://youtu.be/dQw4w9WgXcQ',
        ])->assertRedirect();

        $tugas = Assignment::first();
        $this->assertSame('Latihan Pecahan', $tugas->title);
        $this->assertSame($due->format('Y-m-d H:i'), $tugas->due_at->format('Y-m-d H:i'));
        $this->assertSame(2, $tugas->attachments()->count());

        $this->actingAs($this->ani)->get("/kelas/{$this->kelas->id}")
            ->assertSee('Latihan Pecahan')
            ->assertSee('Belum dikerjakan');
    }

    public function test_validasi_buat_tugas(): void
    {
        $this->actingAs($this->guru)->post("/kelas/{$this->kelas->id}/tugas", [
            'title' => '',
            'due_at' => now()->subDay()->format('Y-m-d\TH:i'),
            'max_score' => 0,
        ])->assertSessionHasErrors([
            'title',
            'due_at' => 'Tenggat harus setelah waktu sekarang.',
            'max_score' => 'Nilai maksimal harus angka 1 sampai 100.',
        ]);

        foreach ([101, 'abc'] as $bad) {
            $this->actingAs($this->guru)->post("/kelas/{$this->kelas->id}/tugas", [
                'title' => 'Tugas', 'due_at' => now()->addDay()->format('Y-m-d\TH:i'), 'max_score' => $bad,
            ])->assertSessionHasErrors(['max_score' => 'Nilai maksimal harus angka 1 sampai 100.']);
        }

        $this->assertSame(0, Assignment::count());
    }

    public function test_guru_ubah_tugas(): void
    {
        $tugas = $this->tugas(['title' => 'Latihan', 'max_score' => 100]);
        $due = now()->addDays(5)->setTime(23, 59);

        $this->actingAs($this->guru)->get($this->url($tugas))->assertSee('Ubah Tugas');
        $this->actingAs($this->guru)->get($this->url($tugas, '/ubah'))->assertOk()->assertSee('Simpan Perubahan');

        $this->actingAs($this->guru)->put($this->url($tugas), [
            'title' => 'Latihan Pecahan',
            'instructions' => 'Halaman 15.',
            'due_at' => $due->format('Y-m-d\TH:i'),
            'max_score' => 50,
            'links' => 'https://youtu.be/dQw4w9WgXcQ',
        ])->assertRedirect($this->url($tugas))
            ->assertSessionHas('status', 'Tugas berhasil diubah.');

        $tugas->refresh();
        $this->assertSame('Latihan Pecahan', $tugas->title);
        $this->assertSame(50, $tugas->max_score);
        $this->assertSame($due->format('Y-m-d H:i'), $tugas->due_at->format('Y-m-d H:i'));
        $this->assertSame(1, $tugas->attachments()->count());
    }

    public function test_ubah_tenggat_menghitung_ulang_status(): void
    {
        $tugas = $this->tugas(['due_at' => now()->subDay()]);
        $this->assertSame(AssignmentStatus::Terlambat, $tugas->statusFor($this->ani));

        $this->actingAs($this->guru)->put($this->url($tugas), [
            'title' => $tugas->title,
            'due_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'max_score' => 100,
        ])->assertSessionHasNoErrors();

        $this->assertSame(AssignmentStatus::BelumDikerjakan, $tugas->fresh()->statusFor($this->ani));
    }

    public function test_ubah_tugas_lewat_tenggat_tanpa_ganti_tenggat_boleh(): void
    {
        $tugas = $this->tugas(['due_at' => now()->subDay()->setSeconds(0)]);

        $this->actingAs($this->guru)->put($this->url($tugas), [
            'title' => 'Judul baru',
            'due_at' => $tugas->due_at->format('Y-m-d\TH:i'),
            'max_score' => 100,
        ])->assertSessionHasNoErrors();

        $this->actingAs($this->guru)->put($this->url($tugas), [
            'title' => 'Judul baru',
            'due_at' => now()->subHour()->format('Y-m-d\TH:i'),
            'max_score' => 100,
        ])->assertSessionHasErrors(['due_at' => 'Tenggat harus setelah waktu sekarang.']);

        $this->assertSame('Judul baru', $tugas->fresh()->title);
    }

    public function test_nilai_maksimal_tidak_boleh_di_bawah_nilai_yang_sudah_diberikan(): void
    {
        $tugas = $this->tugas(['max_score' => 100]);
        Grade::create(['assignment_id' => $tugas->id, 'student_id' => $this->ani->id, 'score' => 80]);

        $this->actingAs($this->guru)->put($this->url($tugas), [
            'title' => $tugas->title,
            'due_at' => $tugas->due_at->format('Y-m-d\TH:i'),
            'max_score' => 50,
        ])->assertSessionHasErrors(['max_score' => 'Nilai maksimal tidak boleh di bawah nilai yang sudah diberikan (80).']);

        $this->assertSame(100, $tugas->fresh()->max_score);
    }

    public function test_siswa_dan_guru_lain_tidak_bisa_ubah_tugas(): void
    {
        $tugas = $this->tugas(['title' => 'Asli']);
        $guruLain = User::factory()->create(['role' => Role::Guru]);
        $payload = ['title' => 'X', 'due_at' => now()->addDay()->format('Y-m-d\TH:i'), 'max_score' => 100];

        $this->actingAs($this->ani)->get($this->url($tugas, '/ubah'))->assertForbidden();
        $this->actingAs($this->ani)->put($this->url($tugas), $payload)->assertForbidden();
        $this->actingAs($guruLain)->put($this->url($tugas), $payload)->assertForbidden();

        $this->assertSame('Asli', $tugas->fresh()->title);
    }

    public function test_siswa_dan_guru_lain_tidak_bisa_buat_tugas(): void
    {
        $guruLain = User::factory()->create(['role' => Role::Guru]);
        $data = ['title' => 'X', 'due_at' => now()->addDay()->format('Y-m-d\TH:i'), 'max_score' => 100];

        $this->actingAs($this->ani)->post("/kelas/{$this->kelas->id}/tugas", $data)->assertForbidden();
        $this->actingAs($guruLain)->post("/kelas/{$this->kelas->id}/tugas", $data)->assertForbidden();
        $this->assertSame(0, Assignment::count());
    }

    // ---------- Hal 6: kumpul foto ----------

    public function test_siswa_kumpul_beberapa_foto_status_jadi_diserahkan(): void
    {
        $tugas = $this->tugas();

        $this->actingAs($this->ani)->get($this->url($tugas))->assertOk()->assertSee('Belum dikerjakan')->assertSee('Ambil Foto');

        $this->actingAs($this->ani)->post($this->url($tugas, '/kumpul'), [
            'files' => [UploadedFile::fake()->image('hal1.jpg'), UploadedFile::fake()->image('hal2.jpg')],
            'body' => 'Sudah saya kerjakan.',
        ])->assertSessionHas('status', 'Tugas berhasil diserahkan!');

        $submission = Submission::first();
        $this->assertSame(2, $submission->files()->count());
        Storage::disk('local')->assertExists($submission->files()->first()->path);
        $this->assertSame(AssignmentStatus::Diserahkan, $tugas->statusFor($this->ani));
        $this->assertFalse($submission->isLate());

        $this->actingAs($this->ani)->get($this->url($tugas))->assertSee('Tugas yang kamu serahkan')->assertDontSee('Ambil Foto');
    }

    public function test_tidak_bisa_kumpul_dua_kali(): void
    {
        $tugas = $this->tugas();
        $this->actingAs($this->ani)->post($this->url($tugas, '/kumpul'), ['files' => [UploadedFile::fake()->image('a.jpg')]]);

        $this->actingAs($this->ani)->post($this->url($tugas, '/kumpul'), ['files' => [UploadedFile::fake()->image('b.jpg')]])
            ->assertSessionHasErrors(['files' => 'Tugas ini sudah kamu serahkan.']);

        $this->assertSame(1, SubmissionFile::count());
    }

    public function test_validasi_kumpul(): void
    {
        $tugas = $this->tugas();

        $this->actingAs($this->ani)->post($this->url($tugas, '/kumpul'), [])
            ->assertSessionHasErrors(['files' => 'Ambil foto tugasmu atau tulis jawaban dulu.']);

        $this->actingAs($this->ani)->post($this->url($tugas, '/kumpul'), ['files' => [UploadedFile::fake()->create('a.exe', 5)]])
            ->assertSessionHasErrors('files.0');

        $this->actingAs($this->ani)->post($this->url($tugas, '/kumpul'), ['files' => [UploadedFile::fake()->image('besar.jpg')->size(10241)]])
            ->assertSessionHasErrors(['files.0' => 'Ukuran file maksimal 10 MB.']);

        $this->assertSame(0, Submission::count());
    }

    public function test_lewat_tenggat_status_terlambat_dan_tetap_bisa_kumpul_ditandai(): void
    {
        $tugas = $this->tugas(['due_at' => now()->subHour()]);

        $this->assertSame(AssignmentStatus::Terlambat, $tugas->statusFor($this->ani));
        $this->actingAs($this->ani)->get($this->url($tugas))->assertSee('Terlambat');

        $this->actingAs($this->ani)->post($this->url($tugas, '/kumpul'), ['files' => [UploadedFile::fake()->image('a.jpg')]])
            ->assertSessionHas('status', 'Tugas diserahkan (terlambat). Guru tetap bisa menilainya.');

        $this->assertTrue(Submission::first()->isLate());
        $this->assertSame(AssignmentStatus::Diserahkan, $tugas->statusFor($this->ani));
    }

    public function test_siswa_luar_kelas_tidak_bisa_lihat_atau_kumpul(): void
    {
        $tugas = $this->tugas();
        $luar = User::factory()->create(['role' => Role::Siswa]);

        $this->actingAs($luar)->get($this->url($tugas))->assertForbidden();
        $this->actingAs($luar)->post($this->url($tugas, '/kumpul'), ['files' => [UploadedFile::fake()->image('a.jpg')]])->assertForbidden();
        $this->actingAs($this->guru)->post($this->url($tugas, '/kumpul'), ['body' => 'x'])->assertForbidden();
    }

    public function test_foto_pengumpulan_hanya_bisa_dibuka_pemilik_dan_guru_pengajar(): void
    {
        $tugas = $this->tugas();
        $this->actingAs($this->ani)->post($this->url($tugas, '/kumpul'), ['files' => [UploadedFile::fake()->image('a.jpg')]]);
        $file = SubmissionFile::first();

        $this->actingAs($this->ani)->get("/berkas-pengumpulan/{$file->id}")->assertOk();
        $this->actingAs($this->guru)->get("/berkas-pengumpulan/{$file->id}")->assertOk();

        $this->actingAs($this->budi)->get("/berkas-pengumpulan/{$file->id}")->assertForbidden(); // teman sekelas
        $this->actingAs(User::factory()->create(['role' => Role::Guru]))->get("/berkas-pengumpulan/{$file->id}")->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => Role::Admin]))->get("/berkas-pengumpulan/{$file->id}")->assertForbidden();
    }

    // ---------- Hal 7: pengumpulan & penilaian ----------

    public function test_guru_lihat_daftar_sudah_dan_belum_mengumpulkan(): void
    {
        $tugas = $this->tugas();
        $this->actingAs($this->ani)->post($this->url($tugas, '/kumpul'), ['files' => [UploadedFile::fake()->image('a.jpg')]]);

        $this->actingAs($this->guru)->get($this->url($tugas))
            ->assertOk()
            ->assertSee('Sudah mengumpulkan (1)')
            ->assertSee('Belum mengumpulkan (1)')
            ->assertSeeInOrder(['Sudah mengumpulkan (1)', 'Ani', 'Belum mengumpulkan (1)', 'Budi']);
    }

    public function test_pengumpulan_terlambat_ditandai_di_daftar_guru(): void
    {
        $tugas = $this->tugas(['due_at' => now()->addMinute()]);
        $this->travel(2)->minutes();
        $this->actingAs($this->ani)->post($this->url($tugas, '/kumpul'), ['files' => [UploadedFile::fake()->image('a.jpg')]]);

        $this->actingAs($this->guru)->get($this->url($tugas))->assertSeeInOrder(['Ani', 'Terlambat']);
    }

    public function test_guru_beri_nilai_status_jadi_dinilai_dan_siswa_lihat_nilainya(): void
    {
        $tugas = $this->tugas();
        $this->actingAs($this->ani)->post($this->url($tugas, '/kumpul'), ['files' => [UploadedFile::fake()->image('a.jpg')]]);

        $this->actingAs($this->guru)->get($this->url($tugas, "/siswa/{$this->ani->id}"))->assertOk()->assertSee('Ani');

        $this->actingAs($this->guru)->put($this->url($tugas, "/siswa/{$this->ani->id}"), [
            'score' => 90, 'feedback' => 'Bagus, nomor 3 dicek lagi.',
        ])->assertRedirect($this->url($tugas));

        $this->assertSame(AssignmentStatus::Dinilai, $tugas->statusFor($this->ani));

        $this->actingAs($this->ani)->get($this->url($tugas))
            ->assertSee('Dinilai')->assertSee('90')->assertSee('Bagus, nomor 3 dicek lagi.');

        // Nilai bisa diperbarui, tetap satu baris
        $this->actingAs($this->guru)->put($this->url($tugas, "/siswa/{$this->ani->id}"), ['score' => 95]);
        $this->assertSame(95, Grade::first()->score);
        $this->assertSame(1, Grade::count());

        // Sudah dinilai: tidak bisa kumpul lagi
        $this->actingAs($this->ani)->post($this->url($tugas, '/kumpul'), ['body' => 'lagi'])->assertSessionHasErrors('files');
    }

    public function test_nilai_harus_0_sampai_nilai_maksimal(): void
    {
        $tugas = $this->tugas(['max_score' => 50]);

        $this->actingAs($this->guru)->put($this->url($tugas, "/siswa/{$this->ani->id}"), ['score' => 51])
            ->assertSessionHasErrors(['score' => 'Nilai paling besar 50.']);
        $this->actingAs($this->guru)->put($this->url($tugas, "/siswa/{$this->ani->id}"), ['score' => -1])
            ->assertSessionHasErrors('score');

        $this->assertSame(0, Grade::count());
    }

    public function test_setelah_menilai_lanjut_ke_siswa_berikutnya(): void
    {
        $tugas = $this->tugas();
        $this->actingAs($this->ani)->post($this->url($tugas, '/kumpul'), ['body' => 'a']);
        $this->actingAs($this->budi)->post($this->url($tugas, '/kumpul'), ['body' => 'b']);

        $this->actingAs($this->guru)->put($this->url($tugas, "/siswa/{$this->ani->id}"), ['score' => 80])
            ->assertRedirect($this->url($tugas, "/siswa/{$this->budi->id}"));
    }

    public function test_hanya_guru_pengajar_yang_bisa_menilai(): void
    {
        $tugas = $this->tugas();
        $guruLain = User::factory()->create(['role' => Role::Guru]);

        $this->actingAs($guruLain)->put($this->url($tugas, "/siswa/{$this->ani->id}"), ['score' => 100])->assertForbidden();
        $this->actingAs($this->ani)->put($this->url($tugas, "/siswa/{$this->ani->id}"), ['score' => 100])->assertForbidden();

        // Siswa bukan anggota kelas → 404
        $luar = User::factory()->create(['role' => Role::Siswa]);
        $this->actingAs($this->guru)->get($this->url($tugas, "/siswa/{$luar->id}"))->assertNotFound();

        // Tugas kelas lain lewat URL kelas sendiri → 404
        $tugasLain = Assignment::factory()->create();
        $this->actingAs($this->guru)->get("/kelas/{$this->kelas->id}/tugas/{$tugasLain->id}/siswa/{$this->ani->id}")->assertNotFound();

        $this->assertSame(0, Grade::count());
    }

    public function test_beranda_guru_menampilkan_yang_perlu_dinilai(): void
    {
        $tugas = $this->tugas();

        $this->actingAs($this->guru)->get('/guru/beranda')->assertDontSee('Perlu dinilai');

        $this->actingAs($this->ani)->post($this->url($tugas, '/kumpul'), ['body' => 'a']);
        $this->actingAs($this->budi)->post($this->url($tugas, '/kumpul'), ['body' => 'b']);

        $this->actingAs($this->guru)->get('/guru/beranda')
            ->assertSee('Perlu dinilai')
            ->assertSee('2 tugas perlu dinilai')
            ->assertSee($this->url($tugas, "/siswa/{$this->ani->id}"), false);

        $this->actingAs($this->guru)->put($this->url($tugas, "/siswa/{$this->ani->id}"), ['score' => 80]);
        $this->actingAs($this->guru)->get('/guru/beranda')->assertSee('1 tugas perlu dinilai');
    }

    public function test_tugas_yang_sudah_dikumpulkan_tidak_bisa_dihapus(): void
    {
        $tugas = $this->tugas();
        $this->actingAs($this->ani)->post($this->url($tugas, '/kumpul'), ['files' => [UploadedFile::fake()->image('a.jpg')]]);
        $path = SubmissionFile::first()->path;

        $this->actingAs($this->guru)->get($this->url($tugas))
            ->assertDontSee('Hapus Tugas')
            ->assertSee('Tugas ini sudah dikumpulkan siswa, jadi tidak bisa dihapus.');

        $this->actingAs($this->guru)->delete($this->url($tugas))
            ->assertRedirect($this->url($tugas))
            ->assertSessionHasErrors(['tugas' => 'Tugas ini sudah dikumpulkan siswa, jadi tidak bisa dihapus.']);

        $this->assertSame(1, Assignment::count());
        $this->assertSame(1, Submission::count());
        Storage::disk('local')->assertExists($path);
    }

    public function test_hapus_tugas_yang_belum_dikumpulkan(): void
    {
        $tugas = $this->tugas();

        $this->actingAs($this->guru)->get($this->url($tugas))->assertSee('Hapus Tugas');

        $this->actingAs($this->guru)->delete($this->url($tugas))->assertRedirect("/kelas/{$this->kelas->id}");

        $this->assertSame(0, Assignment::count());
    }

    public function test_empat_status(): void
    {
        $tugas = $this->tugas();
        $this->assertSame(AssignmentStatus::BelumDikerjakan, $tugas->statusFor($this->ani));

        $this->assertSame(AssignmentStatus::Terlambat, $this->tugas(['due_at' => now()->subMinute()])->statusFor($this->ani));

        Submission::create(['assignment_id' => $tugas->id, 'student_id' => $this->ani->id, 'submitted_at' => now()]);
        $this->assertSame(AssignmentStatus::Diserahkan, $tugas->statusFor($this->ani));

        Grade::create(['assignment_id' => $tugas->id, 'student_id' => $this->ani->id, 'score' => 80]);
        $this->assertSame(AssignmentStatus::Dinilai, $tugas->statusFor($this->ani));
    }
}
