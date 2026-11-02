<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Daftar mata pelajaran, dikelola admin (tidak di-hardcode).
     * Isi awal: mapel SD yang umum, bisa diubah admin kapan saja.
     */
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        $now = now();

        DB::table('subjects')->insert(array_map(fn (string $name) => [
            'name' => $name,
            'created_at' => $now,
            'updated_at' => $now,
        ], [
            'Pendidikan Agama',
            'Pendidikan Pancasila',
            'Bahasa Indonesia',
            'Matematika',
            'IPAS',
            'PJOK',
            'Seni dan Budaya',
            'Bahasa Inggris',
            'Muatan Lokal',
        ]));
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
