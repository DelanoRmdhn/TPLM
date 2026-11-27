<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->admin = User::factory()->create(['role' => Role::Admin]);
    }

    // ---------- Beranda admin ----------

    public function test_beranda_admin_menampilkan_ringkasan(): void
    {
        User::factory()->count(3)->create(['role' => Role::Siswa]);
        User::factory()->create(['role' => Role::Siswa, 'is_active' => false]);
        $guru = User::factory()->create(['role' => Role::Guru]);
        User::factory()->count(2)->create(['role' => Role::Guru, 'is_active' => false]);
        Classroom::factory()->count(2)->create(['teacher_id' => $guru->id]);
        Classroom::factory()->create(['teacher_id' => $guru->id, 'archived_at' => now()]);

        $this->actingAs($this->admin)->get('/admin/beranda')
            ->assertOk()
            ->assertSee('2 akun guru menunggu diaktifkan')
            ->assertSeeInOrder(['3', 'Siswa aktif', '1', 'Guru aktif', '2', 'Kelas berjalan', '1', 'Kelas diarsipkan'])
            ->assertSee('Status: Ditutup')
            ->assertSee(route('admin.kelas.index'), false);
    }

    public function test_tanpa_guru_menunggu_tidak_ada_peringatan(): void
    {
        $this->actingAs($this->admin)->get('/admin/beranda')
            ->assertOk()
            ->assertDontSee('menunggu diaktifkan');
    }

    // ---------- Daftar kelas & pindah guru ----------

    public function test_daftar_kelas_berjalan_dan_arsip(): void
    {
        $guru = User::factory()->create(['role' => Role::Guru, 'name' => 'Bu Siti']);
        Classroom::factory()->create(['teacher_id' => $guru->id, 'name' => 'Matematika 4A']);
        $arsip = Classroom::factory()->create(['teacher_id' => $guru->id, 'name' => 'IPAS Lama', 'archived_at' => now()]);

        $this->actingAs($this->admin)->get('/admin/kelas')
            ->assertOk()
            ->assertSee('Kelas berjalan (1)')
            ->assertSee('Matematika 4A')
            ->assertSee('Bu Siti')
            ->assertDontSee('IPAS Lama')
            // Admin tidak membuka isi kelas
            ->assertDontSee(route('kelas.show', $arsip), false);

        $this->actingAs($this->admin)->get('/admin/kelas?status=arsip')
            ->assertSee('IPAS Lama')
            ->assertDontSee('Matematika 4A');

        $this->actingAs($this->admin)->get('/admin/kelas?q=siti')->assertSee('Matematika 4A');
    }

    public function test_admin_pindahkan_kelas_ke_guru_lain(): void
    {
        $lama = User::factory()->create(['role' => Role::Guru, 'name' => 'Bu Siti']);
        $baru = User::factory()->create(['role' => Role::Guru, 'name' => 'Pak Joko']);
        $kelas = Classroom::factory()->create(['teacher_id' => $lama->id, 'name' => 'Matematika 4A']);

        $this->actingAs($this->admin)->put("/admin/kelas/{$kelas->id}/guru", ['teacher_id' => $baru->id])
            ->assertSessionHas('status', 'Kelas Matematika 4A sekarang diajar Pak Joko.');

        $this->assertSame($baru->id, $kelas->fresh()->teacher_id);
        $this->actingAs($baru)->get("/kelas/{$kelas->id}")->assertOk();
        $this->actingAs($lama)->get("/kelas/{$kelas->id}")->assertForbidden();
    }

    public function test_pindah_guru_hanya_ke_guru_aktif(): void
    {
        $guru = User::factory()->create(['role' => Role::Guru]);
        $kelas = Classroom::factory()->create(['teacher_id' => $guru->id]);
        $nonaktif = User::factory()->create(['role' => Role::Guru, 'is_active' => false]);
        $siswa = User::factory()->create(['role' => Role::Siswa]);

        foreach ([$nonaktif->id, $siswa->id, $this->admin->id, 9999] as $id) {
            $this->actingAs($this->admin)->put("/admin/kelas/{$kelas->id}/guru", ['teacher_id' => $id])
                ->assertSessionHasErrors(['teacher_id' => 'Guru tidak ditemukan atau belum aktif.']);
        }

        $this->assertSame($guru->id, $kelas->fresh()->teacher_id);
    }

    public function test_guru_dan_siswa_tidak_bisa_kelola_kelas_admin(): void
    {
        $guru = User::factory()->create(['role' => Role::Guru]);
        $kelas = Classroom::factory()->create();

        foreach ([$guru, User::factory()->create(['role' => Role::Siswa])] as $user) {
            $this->actingAs($user)->get('/admin/kelas')->assertForbidden();
            $this->actingAs($user)->put("/admin/kelas/{$kelas->id}/guru", ['teacher_id' => $guru->id])->assertForbidden();
        }

        $this->assertNotSame($guru->id, $kelas->fresh()->teacher_id);
    }

    // ---------- PWA ----------

    public function test_halaman_memuat_manifest_dan_tombol_pasang_aplikasi(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('<link rel="manifest" href="/manifest.webmanifest">', false)
            ->assertSee('data-install-app', false);

        $this->actingAs($this->admin)->get('/admin/beranda')
            ->assertSee('<link rel="manifest" href="/manifest.webmanifest">', false);
    }

    public function test_manifest_valid_dan_ikonnya_ada(): void
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('standalone', $manifest['display']);
        $this->assertContains('192x192', array_column($manifest['icons'], 'sizes'));
        $this->assertContains('512x512', array_column($manifest['icons'], 'sizes'));

        foreach ($manifest['icons'] as $icon) {
            $this->assertFileExists(public_path(ltrim($icon['src'], '/')));
        }

        $this->assertFileExists(public_path('sw.js'));
        $this->assertFileExists(public_path('offline.html'));
    }
}
