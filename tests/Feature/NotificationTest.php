<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\RegistrationSetting;
use App\Models\Rombel;
use App\Models\User;
use App\Notifications\AssignmentPublished;
use App\Notifications\GradePublished;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;

    private User $ani;

    private Classroom $kelas;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('local');

        $rombel = Rombel::findOrCreateByName('4A');
        $this->guru = User::factory()->create(['role' => Role::Guru, 'name' => 'Bu Siti']);
        $this->ani = User::factory()->create(['role' => Role::Siswa, 'name' => 'Ani', 'rombel_id' => $rombel->id]);
        $this->kelas = Classroom::factory()->create(['teacher_id' => $this->guru->id, 'name' => 'Matematika 4A', 'rombel_id' => $rombel->id]);
        $this->kelas->addRombel($rombel);
    }

    /** @return list<string> */
    private function messages(User $user): array
    {
        return $user->notifications()->get()->pluck('data.message')->all();
    }

    // ---------- Pemicu ----------

    public function test_tugas_baru_dikirim_ke_siswa_aktif_di_kelas(): void
    {
        $nonaktif = User::factory()->create(['role' => Role::Siswa, 'is_active' => false]);
        $this->kelas->students()->attach($nonaktif);

        $this->actingAs($this->guru)->post("/kelas/{$this->kelas->id}/tugas", [
            'title' => 'Latihan Pecahan',
            'due_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'max_score' => 100,
        ])->assertRedirect();

        $tugas = Assignment::first();
        $this->assertSame(['Tugas baru di Matematika 4A: Latihan Pecahan'], $this->messages($this->ani));
        $this->assertSame(route('kelas.tugas.show', [$this->kelas, $tugas]), $this->ani->notifications()->first()->data['url']);
        $this->assertSame(0, $nonaktif->notifications()->count());
        $this->assertSame(0, $this->guru->notifications()->count());
    }

    public function test_pengumpulan_dikirim_ke_guru_pengajar(): void
    {
        $tugas = Assignment::factory()->pastDue()->create(['classroom_id' => $this->kelas->id, 'title' => 'Latihan 1']);

        $this->actingAs($this->ani)->post("/kelas/{$this->kelas->id}/tugas/{$tugas->id}/kumpul", [
            'files' => [UploadedFile::fake()->image('a.jpg')],
        ]);

        $this->assertSame(['Ani menyerahkan Latihan 1 (terlambat)'], $this->messages($this->guru));
        $this->assertSame(
            route('kelas.tugas.nilai.edit', [$this->kelas, $tugas, $this->ani]),
            $this->guru->notifications()->first()->data['url'],
        );
    }

    public function test_nilai_dikirim_ke_siswa_saat_dinilai_dan_saat_diubah(): void
    {
        $tugas = Assignment::factory()->create(['classroom_id' => $this->kelas->id, 'title' => 'Latihan 1', 'max_score' => 50]);
        $url = "/kelas/{$this->kelas->id}/tugas/{$tugas->id}/siswa/{$this->ani->id}";

        $this->actingAs($this->guru)->put($url, ['score' => 40]);
        $this->actingAs($this->guru)->put($url, ['score' => 40]); // tanpa perubahan: tidak dikirim lagi
        $this->actingAs($this->guru)->put($url, ['score' => 45]);

        $this->assertEqualsCanonicalizing(
            ['Tugas dinilai: Latihan 1, nilai 40/50', 'Nilai diperbarui: Latihan 1, nilai 45/50'],
            $this->messages($this->ani),
        );
        $this->assertSame(2, $this->ani->notifications()->where('type', GradePublished::class)->count());
    }

    public function test_guru_mendaftar_dikirim_ke_admin_aktif(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $adminNonaktif = User::factory()->create(['role' => Role::Admin, 'is_active' => false]);
        RegistrationSetting::current()->update(['is_open' => true]);

        $this->post('/daftar', [
            'role' => 'guru',
            'name' => 'Pak Joko',
            'username' => 'pakjoko',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect('/daftar/berhasil');

        $this->assertSame(['Pak Joko mendaftar sebagai guru dan menunggu diaktifkan'], $this->messages($admin));
        $this->assertSame(0, $adminNonaktif->notifications()->count());

        // Siswa yang mendaftar langsung aktif: admin tidak perlu diberi tahu
        $this->post('/daftar', [
            'role' => 'siswa',
            'name' => 'Citra',
            'username' => '0012345609',
            'rombel_id' => Rombel::findOrCreateByName('4A')->id,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);
        $this->assertSame(1, $admin->notifications()->count());
    }

    // ---------- Halaman Notifikasi ----------

    public function test_halaman_notifikasi_dan_jumlah_belum_dibaca(): void
    {
        $tugas = Assignment::factory()->create(['classroom_id' => $this->kelas->id, 'title' => 'Latihan 1']);
        $this->ani->notify(new AssignmentPublished($tugas));

        $this->actingAs($this->ani)->get('/siswa/beranda')
            ->assertSee(route('notifikasi.index'), false)
            ->assertSee('1<span class="sr-only"> belum dibaca</span>', false);

        $this->actingAs($this->ani)->get('/notifikasi')
            ->assertOk()
            ->assertSee('Tugas baru di Matematika 4A: Latihan 1')
            ->assertSee('(belum dibaca)')
            ->assertSee('Tandai semua dibaca');
    }

    public function test_buka_notifikasi_menandai_dibaca_dan_menuju_halamannya(): void
    {
        $tugas = Assignment::factory()->create(['classroom_id' => $this->kelas->id]);
        $this->ani->notify(new AssignmentPublished($tugas));
        $notification = $this->ani->notifications()->first();

        $this->actingAs($this->ani)->post("/notifikasi/{$notification->id}/baca")
            ->assertRedirect(route('kelas.tugas.show', [$this->kelas, $tugas]));

        $this->assertTrue($notification->fresh()->read());
    }

    public function test_tidak_bisa_membuka_notifikasi_milik_orang_lain(): void
    {
        $tugas = Assignment::factory()->create(['classroom_id' => $this->kelas->id]);
        $this->ani->notify(new AssignmentPublished($tugas));
        $notification = $this->ani->notifications()->first();

        $this->actingAs($this->guru)->post("/notifikasi/{$notification->id}/baca")->assertNotFound();
        $this->actingAs($this->guru)->get('/notifikasi')->assertDontSee($tugas->title);

        $this->assertTrue($notification->fresh()->unread());
    }

    public function test_tandai_semua_dibaca_hanya_milik_sendiri(): void
    {
        $tugas = Assignment::factory()->create(['classroom_id' => $this->kelas->id]);
        $budi = User::factory()->create(['role' => Role::Siswa]);
        $this->ani->notify(new AssignmentPublished($tugas));
        $this->ani->notify(new AssignmentPublished($tugas));
        $budi->notify(new AssignmentPublished($tugas));

        $this->actingAs($this->ani)->post('/notifikasi/baca-semua')
            ->assertSessionHas('status', 'Semua notifikasi ditandai sudah dibaca.');

        $this->assertSame(0, $this->ani->unreadNotifications()->count());
        $this->assertSame(1, $budi->unreadNotifications()->count());
    }

    public function test_notifikasi_butuh_login(): void
    {
        $this->get('/notifikasi')->assertRedirect('/login');
    }
}
