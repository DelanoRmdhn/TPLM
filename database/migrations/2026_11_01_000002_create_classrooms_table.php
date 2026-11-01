<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kelas (FR-07, FR-08) dan anggota kelas (FR-09, FR-11, PB-07).
     */
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete(); // guru pembuat
            $table->string('name');                         // nama_kelas
            $table->string('subject', 100);                 // mata_pelajaran
            $table->foreignId('rombel_id')->nullable()->constrained()->nullOnDelete(); // rombel
            $table->string('academic_year', 9);             // tahun_ajaran, mis. "2026/2027"
            $table->timestamps();
        });

        // Anggota = siswa. Guru pemilik kelas ada di classrooms.teacher_id.
        Schema::create('classroom_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['classroom_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_members');
        Schema::dropIfExists('classrooms');
    }
};
