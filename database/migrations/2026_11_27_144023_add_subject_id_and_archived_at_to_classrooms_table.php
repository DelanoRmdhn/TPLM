<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kelas menunjuk ke mapel lewat subject_id (bukan lagi teks nama mapel),
     * supaya ganti nama mapel di admin langsung berlaku ke semua kelas.
     * archived_at: kelas tahun ajaran lalu bisa diarsipkan tanpa kehilangan nilai.
     */
    public function up(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('name')->constrained()->restrictOnDelete();
            $table->timestamp('archived_at')->nullable()->after('academic_year');
        });

        DB::transaction(function () {
            $now = now();

            foreach (DB::table('classrooms')->distinct()->pluck('subject') as $name) {
                // Nama mapel yang tidak ada di daftar admin ditambahkan, supaya tidak ada kelas yang kehilangan mapel.
                DB::table('subjects')->insertOrIgnore(['name' => $name, 'created_at' => $now, 'updated_at' => $now]);
                $subjectId = DB::table('subjects')->where('name', $name)->value('id');

                DB::table('classrooms')->where('subject', $name)->update(['subject_id' => $subjectId]);
            }
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn('subject');
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->string('subject', 100)->default('')->after('name');
        });

        DB::table('classrooms')->update([
            'subject' => DB::raw('(select name from subjects where subjects.id = classrooms.subject_id)'),
        ]);

        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subject_id');
            $table->dropColumn('archived_at');
        });
    }
};
