<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Rombel;
use App\Models\Submission;
use App\Models\User;
use App\Services\StudentTasks;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StudentTasksTest extends TestCase
{
    use RefreshDatabase;

    private User $ani;

    private User $budi;

    private Classroom $mtk;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        // Senin siang, supaya "Hari ini/Besok/Minggu ini" pasti
        $this->travelTo(Carbon::parse('2026-11-02 10:00'));

        $rombel = Rombel::findOrCreateByName('4-A');
        $guru = User::factory()->create(['role' => Role::Guru]);
        $this->ani = User::factory()->create(['role' => Role::Siswa, 'rombel_id' => $rombel->id]);
        $this->budi = User::factory()->create(['role' => Role::Siswa, 'rombel_id' => $rombel->id]);
        $this->mtk = Classroom::factory()->create(['teacher_id' => $guru->id, 'name' => 'Matematika 4-A', 'subject' => 'Matematika']);
        $this->mtk->addRombel($rombel);
    }

    private function tugas(string $title, Carbon $due, ?Classroom $kelas = null): Assignment
    {
        return Assignment::factory()->create(['classroom_id' => ($kelas ?? $this->mtk)->id, 'title' => $title, 'due_at' => $due]);
    }

    public function test_tugas_saya_dikelompokkan_dan_diurutkan_dari_tenggat_terdekat(): void
    {
        $this->tugas('Tugas Nanti', now()->addDays(20));
        $this->tugas('Tugas Minggu Ini', now()->addDays(4));
        $this->tugas('Tugas Besok', now()->addDay()->setTime(12, 0));
        $this->tugas('Tugas Hari Ini', now()->setTime(23, 59));
        $this->tugas('Tugas Lewat', now()->subDays(2));

        $this->actingAs($this->ani)->get('/siswa/tugas')
            ->assertOk()
            ->assertSee('Belum selesai (5)')
            ->assertSeeInOrder([
                'Hari ini', 'Tugas Hari Ini', 'Hari ini, 23.59 WIB',
                'Besok', 'Tugas Besok', 'Besok, 12.00 WIB',
                'Minggu ini', 'Tugas Minggu Ini',
                'Nanti', 'Tugas Nanti',
                'Lewat tenggat', 'Tugas Lewat', 'lewat 2 hari', 'Terlambat',
            ]);
    }

    public function test_yang_sudah_dikumpulkan_dan_dinilai_pindah_ke_sudah_selesai(): void
    {
        $a = $this->tugas('Sudah Dikumpul', now()->addDays(2));
        $b = $this->tugas('Sudah Dinilai', now()->addDays(3));
        $this->tugas('Masih Belum', now()->addDays(4));

        Submission::create(['assignment_id' => $a->id, 'student_id' => $this->ani->id, 'submitted_at' => now()]);
        Submission::create(['assignment_id' => $b->id, 'student_id' => $this->ani->id, 'submitted_at' => now()]);
        Grade::create(['assignment_id' => $b->id, 'student_id' => $this->ani->id, 'score' => 90]);

        [$done, $pending] = app(StudentTasks::class)->for($this->ani)->partition->done;
        $this->assertSame(['Masih Belum'], $pending->pluck('assignment.title')->all());
        $this->assertEqualsCanonicalizing(['Sudah Dikumpul', 'Sudah Dinilai'], $done->pluck('assignment.title')->all());

        $this->actingAs($this->ani)->get('/siswa/tugas')
            ->assertSee('Belum selesai (1)')
            ->assertSee('Sudah selesai (2)')
            ->assertSee('Diserahkan')
            ->assertSee('Nilai 90/100')
            ->assertDontSee('>Selesai<', false);
    }

    public function test_hanya_tugas_dari_kelas_yang_diikuti(): void
    {
        $kelasLain = Classroom::factory()->create();
        $this->tugas('Tugas Kelas Lain', now()->addDay(), $kelasLain);
        $this->tugas('Tugas Kelasku', now()->addDay());

        $this->actingAs($this->ani)->get('/siswa/tugas')
            ->assertSee('Tugas Kelasku')
            ->assertDontSee('Tugas Kelas Lain');
    }

    public function test_beranda_siswa_tenggat_terdekat_dan_belum_dikerjakan(): void
    {
        $this->tugas('Lewat Kemarin', now()->subDay());
        $this->tugas('Paling Dekat', now()->addHours(3));
        $this->tugas('Lusa', now()->addDays(2));
        $this->tugas('Minggu Depan', now()->addDays(6));

        $this->actingAs($this->ani)->get('/siswa/beranda')
            ->assertOk()
            // Tenggat terdekat = yang belum lewat
            ->assertSeeInOrder(['Tenggat terdekat', 'Paling Dekat', 'Kerjakan Sekarang'])
            // Belum dikerjakan: maks 3, termasuk yang terlambat
            ->assertSeeInOrder(['Belum dikerjakan', 'Lewat Kemarin', 'Paling Dekat', 'Lusa', 'Kelas Saya'])
            ->assertSee('4 tugas belum dikerjakan')
            ->assertSee(route('siswa.nilai'), false);
    }

    public function test_beranda_tanpa_tugas(): void
    {
        $this->actingAs($this->ani)->get('/siswa/beranda')
            ->assertDontSee('Tenggat terdekat')
            ->assertSee('Tidak ada tugas yang belum dikerjakan.');
    }

    public function test_nilai_saya_hanya_nilai_milik_sendiri(): void
    {
        $a = $this->tugas('Latihan Pecahan', now()->addDay());
        $b = $this->tugas('Latihan Bangun Datar', now()->addDays(2));
        Grade::create(['assignment_id' => $a->id, 'student_id' => $this->ani->id, 'score' => 85, 'feedback' => 'Rapi sekali!']);
        Grade::create(['assignment_id' => $b->id, 'student_id' => $this->budi->id, 'score' => 40, 'feedback' => 'Punya Budi']);

        $this->actingAs($this->ani)->get('/siswa/nilai')
            ->assertOk()
            ->assertSee('Matematika 4-A')
            ->assertSee('Latihan Pecahan')
            ->assertSee('85')
            ->assertSee('Rapi sekali!')
            ->assertDontSee('Latihan Bangun Datar')
            ->assertDontSee('Punya Budi');

        $this->actingAs($this->budi)->get('/siswa/nilai')->assertSee('Punya Budi')->assertDontSee('Rapi sekali!');
    }

    public function test_nilai_saya_kosong(): void
    {
        $this->actingAs($this->ani)->get('/siswa/nilai')->assertSee('Belum ada tugas yang dinilai.');
    }

    public function test_halaman_siswa_tidak_bisa_dibuka_guru_atau_admin(): void
    {
        foreach ([Role::Guru, Role::Admin] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get('/siswa/tugas')->assertForbidden();
            $this->actingAs($user)->get('/siswa/nilai')->assertForbidden();
        }
    }
}
