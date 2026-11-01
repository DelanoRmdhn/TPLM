<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Attachment;
use App\Models\Classroom;
use App\Models\Material;
use App\Models\Rombel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MaterialTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;

    private User $siswa;

    private Classroom $kelas;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('local');

        $rombel = Rombel::findOrCreateByName('4-A');
        $this->guru = User::factory()->create(['role' => Role::Guru]);
        $this->siswa = User::factory()->create(['role' => Role::Siswa, 'rombel_id' => $rombel->id]);
        $this->kelas = Classroom::factory()->create(['teacher_id' => $this->guru->id, 'rombel_id' => $rombel->id]);
        $this->kelas->addRombel($rombel);
    }

    private function pdf(string $name = 'pecahan.pdf'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF");
    }

    public function test_guru_buat_topik_dan_urutkan(): void
    {
        foreach (['Bab 1', 'Bab 2', 'Bab 3'] as $name) {
            $this->actingAs($this->guru)->post("/kelas/{$this->kelas->id}/topik", ['name' => $name]);
        }

        $bab3 = $this->kelas->topics()->where('name', 'Bab 3')->first();
        $this->actingAs($this->guru)->post("/kelas/{$this->kelas->id}/topik/{$bab3->id}/naik");

        $this->assertSame(['Bab 1', 'Bab 3', 'Bab 2'], $this->kelas->topics()->pluck('name')->all());

        $bab1 = $this->kelas->topics()->where('name', 'Bab 1')->first();
        $this->actingAs($this->guru)->post("/kelas/{$this->kelas->id}/topik/{$bab1->id}/naik"); // sudah paling atas
        $this->assertSame(['Bab 1', 'Bab 3', 'Bab 2'], $this->kelas->topics()->pluck('name')->all());
    }

    public function test_guru_unggah_materi_pdf_gambar_dan_youtube(): void
    {
        $topik = $this->kelas->topics()->create(['name' => 'Bab 1', 'position' => 1]);

        $response = $this->actingAs($this->guru)->post("/kelas/{$this->kelas->id}/materi", [
            'title' => 'Mengenal Pecahan',
            'description' => 'Baca dan tonton.',
            'topic_id' => $topik->id,
            'files' => [$this->pdf(), UploadedFile::fake()->image('gambar.png')],
            'links' => "https://youtu.be/dQw4w9WgXcQ\nhttps://www.youtube.com/watch?v=abcdefghijk",
        ]);

        $material = Material::first();
        $response->assertRedirect("/kelas/{$this->kelas->id}/materi/{$material->id}");

        $this->assertSame($topik->id, $material->topic_id);
        $this->assertSame(4, $material->attachments()->count());

        $file = $material->attachments()->where('type', 'file')->first();
        Storage::disk('local')->assertExists($file->path);
        $this->assertStringStartsWith("materi/{$this->kelas->id}/", $file->path);
    }

    public function test_materi_tampil_di_halaman_kelas_per_topik(): void
    {
        $topik = $this->kelas->topics()->create(['name' => 'Bab 1: Pecahan', 'position' => 1]);
        $this->kelas->materials()->create(['topic_id' => $topik->id, 'author_id' => $this->guru->id, 'title' => 'Mengenal Pecahan']);
        $this->kelas->materials()->create(['author_id' => $this->guru->id, 'title' => 'Info Umum']);

        $this->actingAs($this->siswa)->get("/kelas/{$this->kelas->id}")
            ->assertOk()
            ->assertSeeInOrder(['Bab 1: Pecahan', 'Mengenal Pecahan', 'Tanpa topik', 'Info Umum'])
            ->assertDontSee('+ Buat Materi');

        $this->actingAs($this->guru)->get("/kelas/{$this->kelas->id}")->assertSee('+ Buat Materi');
    }

    public function test_buat_materi_dengan_topik_baru(): void
    {
        $this->actingAs($this->guru)->post("/kelas/{$this->kelas->id}/materi", [
            'title' => 'Video', 'new_topic' => 'Bab 2: Bangun Datar', 'links' => 'https://youtu.be/dQw4w9WgXcQ',
        ]);

        $this->assertSame('Bab 2: Bangun Datar', Material::first()->topic->name);
    }

    public function test_validasi_file_tautan_dan_isi_kosong(): void
    {
        $url = "/kelas/{$this->kelas->id}/materi";

        $this->actingAs($this->guru)->post($url, [
            'title' => 'X', 'files' => [UploadedFile::fake()->create('virus.exe', 10)],
        ])->assertSessionHasErrors(['files.0' => 'File harus PDF, JPG, PNG, atau DOCX.']);

        $this->actingAs($this->guru)->post($url, [
            'title' => 'X', 'files' => [UploadedFile::fake()->create('besar.pdf', 10241, 'application/pdf')],
        ])->assertSessionHasErrors('files.0');

        $this->actingAs($this->guru)->post($url, [
            'title' => 'X', 'links' => 'https://example.com/video',
        ])->assertSessionHasErrors(['links' => 'Tautan bukan video YouTube: https://example.com/video']);

        $this->actingAs($this->guru)->post($url, ['title' => 'X'])->assertSessionHasErrors('description');

        $this->assertSame(0, Material::count());
    }

    public function test_siswa_buka_pdf_dan_gambar_langsung_di_browser_dan_bisa_unduh(): void
    {
        $this->actingAs($this->guru)->post("/kelas/{$this->kelas->id}/materi", [
            'title' => 'Materi', 'files' => [$this->pdf(), UploadedFile::fake()->image('foto.jpg')],
        ]);
        [$pdf, $img] = Material::first()->attachments()->get()->all();

        $this->actingAs($this->siswa)->get("/kelas/{$this->kelas->id}/materi/".Material::first()->id)
            ->assertOk()->assertSee('pecahan.pdf')->assertSee('foto.jpg');

        $open = $this->actingAs($this->siswa)->get("/lampiran/{$pdf->id}")->assertOk();
        $this->assertStringStartsWith('inline', $open->headers->get('Content-Disposition'));
        $this->assertSame('application/pdf', $open->headers->get('Content-Type'));

        $this->assertStringStartsWith('inline', $this->actingAs($this->siswa)->get("/lampiran/{$img->id}")->headers->get('Content-Disposition'));

        $download = $this->actingAs($this->siswa)->get("/lampiran/{$pdf->id}?unduh=1")->assertOk();
        $this->assertStringStartsWith('attachment', $download->headers->get('Content-Disposition'));
    }

    public function test_docx_selalu_diunduh(): void
    {
        $docx = UploadedFile::fake()->createWithContent('lembar-kerja.docx', file_get_contents(base_path('tests/Fixtures/contoh.docx')));

        $this->actingAs($this->guru)->post("/kelas/{$this->kelas->id}/materi", ['title' => 'LKS', 'files' => [$docx]])
            ->assertSessionHasNoErrors();

        $attachment = Attachment::first();
        $this->assertStringStartsWith('attachment', $this->actingAs($this->siswa)->get("/lampiran/{$attachment->id}")->headers->get('Content-Disposition'));
    }

    public function test_orang_luar_kelas_tidak_bisa_buka_materi_atau_file(): void
    {
        $this->actingAs($this->guru)->post("/kelas/{$this->kelas->id}/materi", ['title' => 'Rahasia', 'files' => [$this->pdf()]]);
        $material = Material::first();
        $file = $material->attachments()->first();

        $siswaLuar = User::factory()->create(['role' => Role::Siswa]);
        $guruLain = User::factory()->create(['role' => Role::Guru]);
        $admin = User::factory()->create(['role' => Role::Admin]);

        foreach ([$siswaLuar, $guruLain, $admin] as $user) {
            $this->actingAs($user)->get("/kelas/{$this->kelas->id}")->assertForbidden();
            $this->actingAs($user)->get("/kelas/{$this->kelas->id}/materi/{$material->id}")->assertForbidden();
            $this->actingAs($user)->get("/lampiran/{$file->id}")->assertForbidden();
        }

        auth()->logout();
        $this->get("/lampiran/{$file->id}")->assertRedirect('/login');
    }

    public function test_materi_kelas_lain_tidak_bisa_dibuka_lewat_url_kelas_sendiri(): void
    {
        $kelasLain = Classroom::factory()->create();
        $materiLain = $kelasLain->materials()->create(['author_id' => $kelasLain->teacher_id, 'title' => 'Punya kelas lain']);

        $this->actingAs($this->siswa)->get("/kelas/{$this->kelas->id}/materi/{$materiLain->id}")->assertNotFound();
    }

    public function test_siswa_dan_guru_lain_tidak_bisa_buat_atau_hapus_materi(): void
    {
        $material = $this->kelas->materials()->create(['author_id' => $this->guru->id, 'title' => 'Materi']);
        $guruLain = User::factory()->create(['role' => Role::Guru]);

        $this->actingAs($this->siswa)->get("/kelas/{$this->kelas->id}/materi/buat")->assertForbidden();
        $this->actingAs($this->siswa)->post("/kelas/{$this->kelas->id}/materi", ['title' => 'X', 'description' => 'Y'])->assertForbidden();
        $this->actingAs($guruLain)->post("/kelas/{$this->kelas->id}/materi", ['title' => 'X', 'description' => 'Y'])->assertForbidden();
        $this->actingAs($guruLain)->delete("/kelas/{$this->kelas->id}/materi/{$material->id}")->assertForbidden();
        $this->actingAs($this->siswa)->post("/kelas/{$this->kelas->id}/topik", ['name' => 'X'])->assertForbidden();

        $this->assertSame(1, Material::count());
    }

    public function test_hapus_materi_ikut_hapus_file(): void
    {
        $this->actingAs($this->guru)->post("/kelas/{$this->kelas->id}/materi", ['title' => 'Materi', 'files' => [$this->pdf()]]);
        $material = Material::first();
        $path = $material->attachments()->first()->path;

        $this->actingAs($this->guru)->delete("/kelas/{$this->kelas->id}/materi/{$material->id}")
            ->assertRedirect("/kelas/{$this->kelas->id}");

        $this->assertSame(0, Material::count());
        $this->assertSame(0, Attachment::count());
        Storage::disk('local')->assertMissing($path);
    }

    public function test_beranda_mengarah_ke_halaman_kelas(): void
    {
        $this->actingAs($this->siswa)->get('/siswa/beranda')->assertSee("/kelas/{$this->kelas->id}\"", false);
        $this->actingAs($this->guru)->get('/guru/beranda')->assertSee("/kelas/{$this->kelas->id}\"", false);
    }

    public function test_parse_tautan_youtube(): void
    {
        $this->assertSame('dQw4w9WgXcQ', Attachment::parseYoutubeId('https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=10s'));
        $this->assertSame('dQw4w9WgXcQ', Attachment::parseYoutubeId('https://youtu.be/dQw4w9WgXcQ'));
        $this->assertSame('dQw4w9WgXcQ', Attachment::parseYoutubeId('https://m.youtube.com/shorts/dQw4w9WgXcQ'));
        $this->assertNull(Attachment::parseYoutubeId('https://vimeo.com/123'));
    }
}
