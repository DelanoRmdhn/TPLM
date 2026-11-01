<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rombel (rombongan belajar), mis. "4-A". Dibuat sebelum users karena siswa menempel ke rombel.
        Schema::create('rombels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20)->unique();
            $table->unsignedTinyInteger('grade_level')->nullable(); // tingkat kelas 1–6
            $table->timestamps();
        });

        // Akun: tanpa email (FR-01, NFR-10). username = NISN (siswa) / NIP atau username (guru, admin).
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username', 50)->unique();
            $table->string('role', 10)->index(); // admin | guru | siswa
            $table->foreignId('rombel_id')->nullable()->constrained()->nullOnDelete(); // hanya siswa
            $table->boolean('is_active')->default(true); // PB-03: nonaktif tidak bisa login
            $table->boolean('must_change_password')->default(false); // PB-02, PB-04
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('rombels');
    }
};
