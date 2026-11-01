<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use App\Support\SimpleXlsx;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ImportAccountTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->admin = User::factory()->create(['username' => 'admin', 'role' => Role::Admin]);
    }

    private function excel(array $rows): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'test').'.xlsx';
        file_put_contents($path, SimpleXlsx::write($rows));

        return new UploadedFile($path, 'data.xlsx', null, null, true);
    }

    public function test_unduh_template_siswa_dan_guru(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/impor/template/siswa')->assertOk();
        $this->assertStringContainsString('template-impor-siswa.xlsx', $response->headers->get('Content-Disposition'));

        $path = tempnam(sys_get_temp_dir(), 'tpl');
        file_put_contents($path, $response->getContent());
        $this->assertSame([1 => ['Nama', 'NISN', 'Rombel']], SimpleXlsx::read($path));

        $this->actingAs($this->admin)->get('/admin/impor/template/guru')->assertOk();
        $this->actingAs($this->admin)->get('/admin/impor/template/lain')->assertNotFound();
    }

    public function test_impor_siswa_dan_laporkan_baris_bermasalah(): void
    {
        User::factory()->create(['username' => '0012345609']); // sudah terdaftar

        $file = $this->excel([
            ['Nama', 'NISN', 'Rombel'],
            ['Ani', '0012345601', '4-A'],
            ['Budi', '12345602', '4-a'],     // nol di depan hilang karena Excel → dipulihkan
            ['', '0012345603', '4-A'],       // nama kosong
            ['Cici', '', '4-A'],             // NISN kosong
            ['Dedi', '0012345601', '4-B'],   // dobel di file
            ['Euis', '0012345609', '4-B'],   // sudah terdaftar
            ['Fajar', '123', '4-B'],         // bukan 10 digit
            ['Gita', '0012345604', ''],      // rombel kosong
            ['', '', ''],                    // baris kosong dilewati
        ]);

        $this->actingAs($this->admin)
            ->post('/admin/impor', ['type' => 'siswa', 'file' => $file])
            ->assertRedirect('/admin/impor/hasil');

        $ani = User::where('username', '0012345601')->first();
        $this->assertSame('4-A', $ani->rombel->name);
        $this->assertTrue($ani->must_change_password);
        $this->assertSame(Role::Siswa, $ani->role);
        $this->assertNotNull(User::where('username', '0012345602')->first());

        $result = session('import_result');
        $this->assertSame(2, $result['created_count']);
        $this->assertSame([4, 5, 6, 7, 8, 9], array_column($result['problems'], 'row'));

        $this->actingAs($this->admin)->get('/admin/impor/hasil')
            ->assertOk()
            ->assertSee('Baris 6:')
            ->assertSee('0012345609 sudah terdaftar.');
    }

    public function test_unduh_daftar_kata_sandi_awal_yang_bisa_dipakai_login(): void
    {
        $file = $this->excel([['Nama', 'NIP atau Username'], ['Pak Joko', '198501012010011002']]);

        $this->actingAs($this->admin)->post('/admin/impor', ['type' => 'guru', 'file' => $file]);

        $response = $this->actingAs($this->admin)->get('/admin/impor/unduh')->assertOk();
        $path = tempnam(sys_get_temp_dir(), 'out');
        file_put_contents($path, $response->getContent());
        $rows = SimpleXlsx::read($path);

        $this->assertSame(['Nama', 'NIP/Username', 'Rombel', 'Kata Sandi Awal'], $rows[1]);
        [$name, $username, , $password] = $rows[2];
        $this->assertSame(['Pak Joko', '198501012010011002'], [$name, $username]);

        $guru = User::where('username', '198501012010011002')->first();
        $this->assertSame(Role::Guru, $guru->role);
        $this->assertTrue(Hash::check($password, $guru->password));

        // Kata sandi awal bisa dipakai login, lalu wajib ganti
        auth()->logout();
        $this->post('/login', ['username' => '198501012010011002', 'password' => $password]);
        $this->assertAuthenticatedAs($guru);
        $this->get('/guru/beranda')->assertRedirect(route('password.create'));
    }

    public function test_nip_yang_rusak_jadi_angka_dilaporkan(): void
    {
        $file = $this->excel([['Nama', 'NIP atau Username'], ['Pak Joko', '1.98501012010011E+17']]);

        $this->actingAs($this->admin)->post('/admin/impor', ['type' => 'guru', 'file' => $file]);

        $this->assertSame(0, session('import_result')['created_count']);
        $this->assertStringContainsString('Teks', session('import_result')['problems'][0]['message']);
    }

    public function test_file_bukan_excel_ditolak(): void
    {
        $file = UploadedFile::fake()->create('data.csv', 10, 'text/csv');

        $this->actingAs($this->admin)
            ->post('/admin/impor', ['type' => 'siswa', 'file' => $file])
            ->assertSessionHasErrors(['file' => 'File harus berformat Excel (.xlsx).']);
    }

    public function test_file_xlsx_rusak_ditolak_dengan_pesan(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'bad').'.xlsx';
        file_put_contents($path, 'bukan zip');
        $file = new UploadedFile($path, 'data.xlsx', null, null, true);

        $this->actingAs($this->admin)
            ->post('/admin/impor', ['type' => 'siswa', 'file' => $file])
            ->assertSessionHasErrors('file');
    }

    public function test_bukan_admin_tidak_bisa_impor(): void
    {
        $guru = User::factory()->create(['role' => Role::Guru]);

        $this->actingAs($guru)->get('/admin/impor')->assertForbidden();
    }
}
