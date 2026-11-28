<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Assignment;
use App\Models\Attachment;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Material;
use App\Models\Rombel;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\AssignmentPublished;
use App\Notifications\GradePublished;
use App\Notifications\SubmissionReceived;
use App\Notifications\TeacherRegistered;
use Illuminate\Database\Seeder;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Data contoh satu sekolah supaya aplikasi terasa "sudah berjalan": 3 guru, 2 rombel berisi siswa,
 * 8 kelas berbagai mapel (+1 kelas arsip tahun lalu), topik, materi berisi file, tugas dengan tenggat
 * lewat/hari ini/besok/minggu depan, pengumpulan (tepat waktu & terlambat), nilai + umpan balik, notifikasi.
 *
 * Siswa demo (NISN 0012345601) sengaja punya campuran status: sudah dinilai, menunggu nilai,
 * belum dikerjakan, dan terlambat. File contoh disimpan di storage/app/private/demo.
 * Hanya untuk development; acak tapi selalu sama hasilnya (mt_srand).
 */
class DemoSchoolSeeder extends Seeder
{
    private const DEMO_DIR = 'demo';

    private const FEEDBACK = [
        'Bagus sekali! Pertahankan ya.',
        'Rapi dan lengkap. Hebat!',
        'Sudah baik, coba periksa lagi nomor 3.',
        'Kerja bagus! Tulisannya lebih rapi dari sebelumnya.',
        'Jawabanmu tepat. Lain kali tulis caranya juga ya.',
        'Mantap! Kamu sudah paham materinya.',
        'Sudah bagus, tapi masih ada yang terlewat. Semangat!',
        null,
        null,
    ];

    /** @var array<int, int> kemampuan tiap siswa (id => 60..96) untuk nilai acak yang masuk akal */
    private array $skill = [];

    private User $demoStudent;

    public function run(): void
    {
        mt_srand(2026);
        Storage::disk('local')->deleteDirectory(self::DEMO_DIR);

        $rombel4A = Rombel::findByName('4A');
        $rombel4B = Rombel::findByName('4B');

        [$siti, $agus, $dewi] = $this->teachers();
        $this->students($rombel4A, $rombel4B);

        // Kelas aktif tahun ini: [guru, nama, mapel, rombel, daftar topik]
        $plans = [
            [$siti, 'Matematika 4A', 'Matematika', $rombel4A, $this->matematika()],
            [$siti, 'Bahasa Indonesia 4A', 'Bahasa Indonesia', $rombel4A, $this->bahasaIndonesia()],
            [$siti, 'IPAS 4A', 'IPAS', $rombel4A, $this->ipas()],
            [$dewi, 'Bahasa Inggris 4A', 'Bahasa Inggris', $rombel4A, $this->bahasaInggris()],
            [$dewi, 'Pendidikan Pancasila 4A', 'Pendidikan Pancasila', $rombel4A, $this->pancasila()],
            [$agus, 'PJOK 4A', 'PJOK', $rombel4A, $this->pjok()],
            [$agus, 'Seni Budaya 4A', 'Seni dan Budaya', $rombel4A, $this->seni()],
            [$agus, 'PJOK 4B', 'PJOK', $rombel4B, $this->pjok()],
        ];

        $year = Classroom::academicYearOptions()[0];
        foreach ($plans as [$teacher, $name, $subject, $rombel, $topics]) {
            $classroom = $this->classroom($teacher, $name, $subject, $rombel, $year);
            $this->fill($classroom, $teacher, $topics);
        }

        // Kelas tahun lalu yang sudah diarsipkan (nilainya tetap muncul di Nilai Saya)
        [$start] = explode('/', $year);
        $archived = $this->classroom($siti, 'Matematika 3A', 'Matematika', $rombel4A, ($start - 1).'/'.$start);
        $this->fill($archived, $siti, $this->matematikaTahunLalu());
        $archived->update(['archived_at' => now()->subMonths(5)]);

        $this->notifications($siti);
    }

    /** @return array{0: User, 1: User, 2: User} */
    private function teachers(): array
    {
        $siti = User::where('username', '198701012010011001')->firstOrFail();
        $siti->update(['name' => 'Ibu Siti Rahmawati']);

        $agus = User::factory()->create(['name' => 'Bapak Agus Setiawan', 'username' => '198903152015021002', 'role' => Role::Guru]);
        $dewi = User::factory()->create(['name' => 'Ibu Dewi Lestari', 'username' => '199101202019032003', 'role' => Role::Guru]);

        // Guru yang mendaftar sendiri dan menunggu diaktifkan admin
        $joko = User::factory()->create(['name' => 'Bapak Joko Santoso', 'username' => 'jokosantoso', 'role' => Role::Guru, 'is_active' => false]);
        $admin = User::where('role', Role::Admin)->first();
        if ($admin) {
            $this->notifyAt($admin, new TeacherRegistered($joko), now()->subHours(5), read: false);
        }

        return [$siti, $agus, $dewi];
    }

    private function students(Rombel $rombel4A, Rombel $rombel4B): void
    {
        $this->demoStudent = User::where('username', '0012345601')->firstOrFail();
        $this->demoStudent->update(['name' => 'Ani Putri Lestari']);
        User::where('username', '0012345602')->update(['name' => 'Bima Pratama']);

        $names4A = ['Citra Ayu', 'Dimas Saputra', 'Eka Wulandari', 'Fajar Nugroho', 'Gita Permata', 'Hana Safitri', 'Irfan Maulana', 'Jihan Aulia', 'Kevin Ramadhan', 'Laras Kinanti'];
        $names4B = ['Maya Sari', 'Naufal Hakim', 'Olivia Putri', 'Putra Wijaya', 'Qori Amalia', 'Rizky Firmansyah', 'Salsa Nabila', 'Tegar Prakoso'];

        $nisn = 3;
        foreach ([[$rombel4A, $names4A], [$rombel4B, $names4B]] as [$rombel, $names]) {
            foreach ($names as $name) {
                User::factory()->create([
                    'name' => $name,
                    'username' => sprintf('00123456%02d', $nisn++),
                    'role' => Role::Siswa,
                    'rombel_id' => $rombel->id,
                ]);
            }
        }

        foreach (User::where('role', Role::Siswa)->pluck('id') as $id) {
            $this->skill[$id] = mt_rand(60, 96);
        }
        $this->skill[$this->demoStudent->id] = 88;
    }

    private function classroom(User $teacher, string $name, string $subject, Rombel $rombel, string $year): Classroom
    {
        $classroom = $teacher->teachingClassrooms()->create([
            'name' => $name,
            'subject_id' => Subject::where('name', $subject)->value('id'),
            'rombel_id' => $rombel->id,
            'academic_year' => $year,
        ]);
        $classroom->addRombel($rombel);

        return $classroom;
    }

    /**
     * Isi kelas: topik, materi, tugas, pengumpulan, nilai.
     *
     * @param  list<array{name: string, materials: list<array>, assignments: list<array>}>  $topics
     */
    private function fill(Classroom $classroom, User $teacher, array $topics): void
    {
        $students = $classroom->students()->get();

        foreach ($topics as $position => $topicPlan) {
            $topic = $classroom->topics()->create(['name' => $topicPlan['name'], 'position' => $position + 1]);

            foreach ($topicPlan['materials'] as $plan) {
                $material = $classroom->materials()->create([
                    'topic_id' => $topic->id,
                    'author_id' => $teacher->id,
                    'title' => $plan['title'],
                    'description' => $plan['description'],
                ]);
                $this->backdate($material, now()->addDays($plan['days'] ?? -14));
                $this->attachFile($material, $plan['file'], $plan['title'], $plan['lines'] ?? []);
            }

            foreach ($topicPlan['assignments'] as $plan) {
                $hour = $plan['hour'] ?? null;
                $due = now()->addDays($plan['due'])->setTime($hour ?? 23, $hour === null ? 59 : 0);
                $assignment = $classroom->assignments()->create([
                    'topic_id' => $topic->id,
                    'author_id' => $teacher->id,
                    'title' => $plan['title'],
                    'instructions' => $plan['instructions'],
                    'due_at' => $due,
                    'max_score' => $plan['max'] ?? 100,
                ]);
                $this->backdate($assignment, $due->copy()->subDays(7)->setTime(7, 30));

                if (isset($plan['file'])) {
                    $this->attachFile($assignment, $plan['file'], $plan['title'].' (soal)', $plan['lines'] ?? []);
                }

                foreach ($students as $student) {
                    $this->work($assignment, $student, $teacher, $plan);
                }
            }
        }
    }

    /**
     * Pengumpulan & nilai satu siswa untuk satu tugas.
     *
     * @param  array{title: string, due: int, demo?: array{0: string, 1?: int, 2?: string}}  $plan
     */
    private function work(Assignment $assignment, User $student, User $teacher, array $plan): void
    {
        $due = $assignment->due_at;
        $isDemo = $student->is($this->demoStudent);

        // Siswa demo: status ditentukan per tugas supaya campurannya jelas
        if ($isDemo) {
            $demo = $plan['demo'] ?? ['none'];
            if ($demo[0] === 'none') {
                return;
            }
            $submittedAt = $due->isPast() ? $due->copy()->subHours(mt_rand(4, 40)) : now()->subHours(mt_rand(2, 20));
            $submission = $this->submit($assignment, $student, $submittedAt, withPhoto: true);
            if ($demo[0] === 'graded') {
                $this->grade($assignment, $student, $teacher, $submission, $demo[1], $demo[2] ?? null);
            }

            return;
        }

        $roll = mt_rand(1, 100);
        if ($due->isPast()) {
            if ($roll > 85) {
                return; // tidak mengumpulkan
            }
            $late = mt_rand(1, 100) <= 18;
            $submittedAt = $late ? $due->copy()->addHours(mt_rand(3, 30)) : $due->copy()->subHours(mt_rand(3, 60));
            if ($submittedAt->isFuture()) {
                $submittedAt = now()->subHour();
            }
            $submission = $this->submit($assignment, $student, $submittedAt, withPhoto: mt_rand(1, 100) <= 55);

            // Tugas yang sudah lama lewat hampir semua sudah dinilai; yang baru lewat sebagian
            $gradeChance = $due->lt(now()->subDays(3)) ? 92 : 35;
            if (mt_rand(1, 100) <= $gradeChance) {
                $this->grade($assignment, $student, $teacher, $submission, null, null);
            }

            return;
        }

        // Tenggat belum lewat: sebagian siswa sudah mengumpulkan lebih awal (menunggu dinilai)
        if ($roll <= 30) {
            $this->submit($assignment, $student, now()->subHours(mt_rand(1, 48))->max($assignment->created_at->copy()->addHour()), withPhoto: mt_rand(1, 100) <= 55);
        }
    }

    private function submit(Assignment $assignment, User $student, Carbon $at, bool $withPhoto): Submission
    {
        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'body' => mt_rand(1, 100) <= 40 ? 'Sudah saya kerjakan, Bu/Pak. Terima kasih.' : null,
            'submitted_at' => $at,
        ]);
        $this->backdate($submission, $at);

        if ($withPhoto || $submission->body === null) {
            $path = self::DEMO_DIR."/pengumpulan/{$assignment->id}/{$student->id}/".Str::uuid().'.jpg';
            $bytes = $this->homeworkPhoto($student->name, $assignment->title);
            Storage::disk('local')->put($path, $bytes);
            $submission->files()->create([
                'path' => $path,
                'original_name' => 'foto-tugas.jpg',
                'mime_type' => 'image/jpeg',
                'size' => strlen($bytes),
            ]);
        }

        return $submission;
    }

    private function grade(Assignment $assignment, User $student, User $teacher, Submission $submission, ?int $score, ?string $feedback): void
    {
        if ($score === null) {
            $base = $this->skill[$student->id] + mt_rand(-8, 6) - ($submission->isLate() ? 5 : 0);
            $score = (int) round(max(45, min(100, $base)) / 100 * $assignment->max_score);
            $feedback = self::FEEDBACK[array_rand(self::FEEDBACK)];
        }

        $grade = Grade::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'graded_by' => $teacher->id,
            'score' => min($score, $assignment->max_score),
            'feedback' => $feedback,
        ]);
        $gradedAt = $submission->submitted_at->copy()->addHours(mt_rand(6, 48));
        $this->backdate($grade, $gradedAt->isFuture() ? now()->subMinutes(30) : $gradedAt);
    }

    private function notifications(User $teacher): void
    {
        $student = $this->demoStudent;

        // Tugas baru seminggu terakhir
        $recent = Assignment::query()
            ->whereIn('classroom_id', $student->classrooms()->notArchived()->select('classrooms.id'))
            ->where('created_at', '>=', now()->subDays(7))
            ->with('classroom')
            ->orderBy('created_at')
            ->get();
        foreach ($recent as $assignment) {
            $this->notifyAt($student, new AssignmentPublished($assignment), $assignment->created_at, read: $assignment->created_at->lt(now()->subDays(3)));
        }

        // Nilai yang baru keluar
        $grades = Grade::where('student_id', $student->id)->with('assignment')->orderBy('updated_at')->get()->slice(-3);
        foreach ($grades as $grade) {
            $this->notifyAt($student, new GradePublished($grade), $grade->updated_at, read: false);
        }

        // Guru: pengumpulan terbaru yang belum dinilai
        $ungraded = Submission::query()
            ->whereIn('assignment_id', Assignment::whereIn('classroom_id', $teacher->teachingClassrooms()->notArchived()->select('id'))->select('id'))
            ->whereNotNull('submitted_at')
            ->whereNotExists(fn ($q) => $q->selectRaw(1)->from('grades')
                ->whereColumn('grades.assignment_id', 'submissions.assignment_id')
                ->whereColumn('grades.student_id', 'submissions.student_id'))
            ->with(['assignment', 'student'])
            ->orderByDesc('submitted_at')
            ->limit(6)
            ->get()
            ->reverse();
        foreach ($ungraded as $submission) {
            $this->notifyAt($teacher, new SubmissionReceived($submission), $submission->submitted_at, read: false);
        }
    }

    /** Simpan notifikasi database dengan waktu di masa lalu (notify() selalu memakai waktu sekarang). */
    private function notifyAt(User $user, Notification $notification, Carbon $at, bool $read): void
    {
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => $notification::class,
            'data' => $notification->toArray($user),
            'read_at' => $read ? $at->copy()->addHours(2) : null,
            'created_at' => $at,
            'updated_at' => $at,
        ]);
    }

    private function backdate(object $model, Carbon $at): void
    {
        $model->timestamps = false;
        $model->forceFill(['created_at' => $at, 'updated_at' => $at])->save();
        $model->timestamps = true;
    }

    /**
     * Lampiran file contoh: "pdf" (lembar materi) atau "png" (poster bergambar).
     *
     * @param  list<string>  $lines
     */
    private function attachFile(Material|Assignment $owner, string $type, string $title, array $lines): void
    {
        $folder = $owner instanceof Material ? 'materi' : 'tugas';
        $path = self::DEMO_DIR."/{$folder}/{$owner->classroom_id}/".Str::uuid().'.'.$type;
        $bytes = $type === 'pdf' ? $this->pdf($title, $lines) : $this->poster($title, $lines);
        Storage::disk('local')->put($path, $bytes);

        $owner->attachments()->create([
            'type' => Attachment::TYPE_FILE,
            'path' => $path,
            'original_name' => Str::slug($title).'.'.$type,
            'mime_type' => $type === 'pdf' ? 'application/pdf' : 'image/png',
            'size' => strlen($bytes),
        ]);
    }

    /**
     * PDF satu halaman berisi judul & beberapa baris teks (tanpa paket tambahan).
     *
     * @param  list<string>  $lines
     */
    private function pdf(string $title, array $lines): string
    {
        $escape = fn (string $text) => str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        $content = "BT /F1 22 Tf 60 770 Td ({$escape($title)}) Tj ET\n";
        $y = 730;
        foreach ($lines as $line) {
            $content .= "BT /F1 13 Tf 60 {$y} Td ({$escape($line)}) Tj ET\n";
            $y -= 24;
        }
        $content .= "BT /F1 10 Tf 60 60 Td (SDN 002 Karangmulya - materi contoh) Tj ET\n";

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            '<< /Length '.strlen($content)." >>\nstream\n{$content}endstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $i => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1)." 0 obj\n{$object}\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= 'xref
0 '.(count($objects) + 1)."\n0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        return $pdf.'trailer << /Size '.(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";
    }

    /**
     * Poster PNG berwarna sederhana untuk materi bergambar.
     *
     * @param  list<string>  $lines
     */
    private function poster(string $title, array $lines): string
    {
        $img = imagecreatetruecolor(320, 200);
        $palette = [[59, 91, 255], [19, 128, 63], [112, 72, 245], [11, 127, 115], [191, 79, 11]];
        [$r, $g, $b] = $palette[mt_rand(0, count($palette) - 1)];
        imagefill($img, 0, 0, imagecolorallocate($img, $r, $g, $b));
        $white = imagecolorallocate($img, 255, 255, 255);
        $soft = imagecolorallocatealpha($img, 255, 255, 255, 100);
        imagefilledellipse($img, 290, 30, 140, 140, $soft);
        imagefilledellipse($img, 30, 190, 120, 120, $soft);
        imagestring($img, 5, 16, 18, Str::limit($title, 34, ''), $white);
        $y = 52;
        foreach ($lines as $line) {
            imagestring($img, 3, 16, $y, '- '.Str::limit($line, 44, ''), $white);
            $y += 20;
        }

        // Perbesar 2x supaya tidak terlalu kecil di HP
        $big = imagescale($img, 640, 400, IMG_NEAREST_NEIGHBOUR);
        ob_start();
        imagepng($big, null, 9);
        imagedestroy($img);
        imagedestroy($big);

        return (string) ob_get_clean();
    }

    /** "Foto" lembar tugas tulisan tangan untuk pengumpulan siswa. */
    private function homeworkPhoto(string $student, string $title): string
    {
        $img = imagecreatetruecolor(240, 320);
        imagefill($img, 0, 0, imagecolorallocate($img, 250, 248, 240));
        $line = imagecolorallocate($img, 190, 205, 235);
        $margin = imagecolorallocate($img, 235, 150, 150);
        $ink = imagecolorallocate($img, 35, 50, 120);
        for ($y = 40; $y < 320; $y += 16) {
            imageline($img, 0, $y, 240, $y, $line);
        }
        imageline($img, 28, 0, 28, 320, $margin);
        imagestring($img, 3, 34, 12, Str::limit($student, 26, ''), $ink);
        imagestring($img, 2, 34, 28, Str::limit($title, 32, ''), $ink);
        for ($n = 1, $y = 44; $y < 300; $n++, $y += 32) {
            imagestring($img, 3, 34, $y, $n.'. '.str_repeat('~', mt_rand(8, 22)), $ink);
        }

        // Sedikit miring seperti difoto dari HP, lalu diperbesar
        $rotated = imagerotate($img, mt_rand(-3, 3), imagecolorallocate($img, 120, 110, 100));
        $big = imagescale($rotated, 480, -1);
        ob_start();
        imagejpeg($big, null, 70);
        imagedestroy($img);
        imagedestroy($rotated);
        imagedestroy($big);

        return (string) ob_get_clean();
    }

    // ---------- Isi kelas per mapel ----------
    // due: selisih hari dari hari ini (negatif = sudah lewat). demo: status siswa demo.

    private function matematika(): array
    {
        return [
            ['name' => 'Bab 1: Pecahan', 'materials' => [
                ['title' => 'Mengenal Pecahan', 'file' => 'pdf', 'days' => -16, 'description' => 'Baca lembar ini dulu sebelum mengerjakan latihan. Pecahan adalah bagian dari keseluruhan.',
                    'lines' => ['Pecahan = bagian dari keseluruhan.', 'Contoh: 1/2 kue artinya kue dibagi 2, diambil 1.', 'Pembilang = angka atas, penyebut = angka bawah.', 'Pecahan senilai: 1/2 = 2/4 = 3/6.']],
            ], 'assignments' => [
                ['title' => 'Latihan Pecahan Senilai', 'due' => -9, 'instructions' => 'Kerjakan soal di buku halaman 12 nomor 1 sampai 5, lalu foto hasilnya.',
                    'demo' => ['graded', 90, 'Bagus sekali, Ani! Cara menyederhanakannya sudah tepat.']],
                ['title' => 'Menyederhanakan Pecahan', 'due' => -2, 'instructions' => 'Sederhanakan 10 pecahan di lembar soal. Tulis caranya ya.', 'file' => 'pdf',
                    'lines' => ['1. 4/8', '2. 6/9', '3. 10/15', '4. 12/16', '5. 8/12'], 'demo' => ['submitted']],
            ]],
            ['name' => 'Bab 2: Bangun Datar', 'materials' => [
                ['title' => 'Keliling dan Luas Persegi', 'file' => 'png', 'days' => -4, 'description' => 'Perhatikan gambar rumus di bawah ini.',
                    'lines' => ['Keliling persegi = 4 x sisi', 'Luas persegi = sisi x sisi', 'Satuan luas: cm persegi']],
            ], 'assignments' => [
                ['title' => 'Mengukur Keliling Benda di Rumah', 'due' => 1, 'instructions' => 'Ukur keliling 3 benda berbentuk persegi di rumahmu (misalnya ubin, buku, bingkai). Tulis hasilnya dan foto bendanya.'],
                ['title' => 'Kuis Luas Persegi', 'due' => 6, 'instructions' => 'Jawab 5 soal cerita tentang luas persegi di buku halaman 30.'],
            ]],
        ];
    }

    private function bahasaIndonesia(): array
    {
        return [
            ['name' => 'Bab 1: Membaca Cerita', 'materials' => [
                ['title' => 'Cerita Si Kancil dan Buaya', 'file' => 'pdf', 'days' => -12, 'description' => 'Bacalah cerita ini bersama orang tuamu.',
                    'lines' => ['Suatu hari Si Kancil ingin menyeberang sungai.', 'Di sungai banyak buaya yang lapar.', 'Kancil mendapat ide yang cerdik...', 'Pesan cerita: gunakan akalmu dengan baik.']],
            ], 'assignments' => [
                ['title' => 'Menulis Ringkasan Cerita', 'due' => -6, 'instructions' => 'Tulis ringkasan cerita Si Kancil dalam 5 kalimat dengan kata-katamu sendiri.',
                    'demo' => ['graded', 85, 'Ringkasannya jelas. Perhatikan huruf kapital di awal kalimat ya.']],
                ['title' => 'Membaca Nyaring', 'due' => 0, 'hour' => 20, 'instructions' => 'Bacakan satu paragraf cerita dengan suara jelas. Minta orang tua menulis catatan di buku, lalu foto catatannya.'],
            ]],
            ['name' => 'Bab 2: Puisi', 'materials' => [], 'assignments' => [
                ['title' => 'Menulis Puisi tentang Ibu', 'due' => 4, 'instructions' => 'Tulis puisi 3 bait tentang ibu atau orang yang kamu sayangi.'],
            ]],
        ];
    }

    private function ipas(): array
    {
        return [
            ['name' => 'Bab 1: Bagian Tubuh Tumbuhan', 'materials' => [
                ['title' => 'Bagian Tumbuhan dan Fungsinya', 'file' => 'png', 'days' => -18, 'description' => 'Akar, batang, daun, bunga. Masing-masing punya tugas!',
                    'lines' => ['Akar: menyerap air dari tanah', 'Batang: menyalurkan air & makanan', 'Daun: tempat membuat makanan', 'Bunga: tempat tumbuh biji']],
            ], 'assignments' => [
                ['title' => 'Menggambar Bagian Tumbuhan', 'due' => -12, 'instructions' => 'Gambar satu tanaman di sekitar rumahmu dan beri nama bagian-bagiannya.',
                    'demo' => ['graded', 92, 'Gambarnya bagus dan lengkap. Keren!']],
                ['title' => 'Pengamatan Kecambah', 'due' => 3, 'max' => 50, 'instructions' => 'Tanam kacang hijau di kapas basah. Foto pertumbuhannya setiap hari selama 3 hari.',
                    'demo' => ['submitted']],
            ]],
            ['name' => 'Bab 2: Gaya dan Gerak', 'materials' => [], 'assignments' => [
                ['title' => 'Percobaan Bola Menggelinding', 'due' => 10, 'instructions' => 'Gelindingkan bola di lantai dan di karpet. Mana yang lebih jauh? Tulis hasilnya.'],
            ]],
        ];
    }

    private function bahasaInggris(): array
    {
        return [
            ['name' => 'Unit 1: My Family', 'materials' => [
                ['title' => 'Family Members', 'file' => 'png', 'days' => -10, 'description' => 'Learn the names of family members in English.',
                    'lines' => ['father = ayah, mother = ibu', 'brother = saudara laki-laki', 'sister = saudara perempuan', 'grandmother = nenek']],
            ], 'assignments' => [
                ['title' => 'My Family Tree', 'due' => -4, 'max' => 50, 'instructions' => 'Draw your family tree and write the English name for each member.',
                    'demo' => ['graded', 45, 'Great job, Ani! Very colorful.']],
            ]],
            ['name' => 'Unit 2: In My Classroom', 'materials' => [], 'assignments' => [
                ['title' => 'Name the Things', 'due' => 2, 'instructions' => 'Write 10 things in your classroom in English. Example: chair, table, board.'],
            ]],
        ];
    }

    private function pancasila(): array
    {
        return [
            ['name' => 'Bab 1: Gotong Royong', 'materials' => [
                ['title' => 'Apa Itu Gotong Royong?', 'file' => 'pdf', 'days' => -8, 'description' => 'Gotong royong artinya bekerja bersama-sama untuk kebaikan bersama.',
                    'lines' => ['Contoh di sekolah: piket kelas bersama.', 'Contoh di rumah: membersihkan rumah bersama keluarga.', 'Contoh di kampung: kerja bakti.']],
            ], 'assignments' => [
                ['title' => 'Cerita Gotong Royong di Rumah', 'due' => -1, 'instructions' => 'Ceritakan kegiatan gotong royong yang kamu lakukan bersama keluarga minggu ini.'],
            ]],
        ];
    }

    private function pjok(): array
    {
        return [
            ['name' => 'Bab 1: Gerak Dasar', 'materials' => [
                ['title' => 'Gerakan Pemanasan', 'file' => 'png', 'days' => -15, 'description' => 'Lakukan pemanasan sebelum olahraga supaya tidak cedera.',
                    'lines' => ['Putar kepala 8 kali', 'Putar lengan 8 kali', 'Lompat di tempat 10 kali', 'Tarik napas dalam-dalam']],
            ], 'assignments' => [
                ['title' => 'Foto Gerakan Pemanasan', 'due' => -7, 'instructions' => 'Minta keluargamu memfoto kamu saat melakukan 2 gerakan pemanasan.',
                    'demo' => ['graded', 95, 'Gerakannya sudah benar. Semangat olahraga!']],
                ['title' => 'Catatan Latihan Lari', 'due' => 5, 'instructions' => 'Lari kecil 5 menit setiap pagi selama 3 hari. Catat waktunya di buku.'],
            ]],
        ];
    }

    private function seni(): array
    {
        return [
            ['name' => 'Bab 1: Menggambar', 'materials' => [
                ['title' => 'Teknik Mewarnai Gradasi', 'file' => 'png', 'days' => -20, 'description' => 'Mewarnai dari warna tua ke muda supaya gambar lebih hidup.',
                    'lines' => ['Mulai dari warna paling tua', 'Tekan pensil warna pelan-pelan', 'Semakin ke ujung, semakin muda']],
            ], 'assignments' => [
                ['title' => 'Menggambar Pemandangan', 'due' => -8, 'instructions' => 'Gambar pemandangan gunung dan sawah, lalu warnai dengan teknik gradasi.',
                    'demo' => ['graded', 88, 'Warnanya cantik! Langitnya bisa dibuat lebih bergradasi.']],
                ['title' => 'Membuat Kolase Daun', 'due' => 9, 'instructions' => 'Kumpulkan daun kering dan tempel menjadi gambar hewan di kertas HVS.'],
            ]],
        ];
    }

    private function matematikaTahunLalu(): array
    {
        return [
            ['name' => 'Bab 1: Penjumlahan dan Pengurangan', 'materials' => [], 'assignments' => [
                ['title' => 'Penjumlahan Bersusun', 'due' => -200, 'instructions' => 'Kerjakan 10 soal penjumlahan bersusun.',
                    'demo' => ['graded', 80, 'Sudah bagus, teliti lagi saat menyimpan angka.']],
                ['title' => 'Perkalian 1 sampai 10', 'due' => -180, 'instructions' => 'Hafalkan dan tulis tabel perkalian 1 sampai 10.',
                    'demo' => ['graded', 86, 'Hafalanmu hebat!']],
            ]],
        ];
    }
}
