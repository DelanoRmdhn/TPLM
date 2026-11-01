<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lampiran guru (polymorphic): dipakai materi dan tugas, nanti juga pengumuman forum.
     * type = file (PDF/gambar/DOCX) atau link (YouTube/tautan lain).
     */
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable'); // attachable_type + attachable_id
            $table->string('type', 10);   // file | link
            $table->string('path')->nullable();          // untuk file: path di storage
            $table->string('original_name')->nullable(); // nama file asli
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size')->nullable(); // byte
            $table->text('url')->nullable();             // untuk link
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
