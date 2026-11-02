<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rombel memakai satu format, mis. "4A" (tanpa strip), lalu diisi 1A–6B dari awal.
     * Rombel lama seperti "4-A" diganti jadi "4A"; kalau dua-duanya ada, digabung.
     */
    public function up(): void
    {
        $now = now();

        DB::transaction(function () use ($now) {
            foreach (DB::table('rombels')->orderBy('id')->get() as $rombel) {
                $name = strtoupper(preg_replace('/[\s\-._\/]+/', '', $rombel->name));

                if ($name === $rombel->name) {
                    continue;
                }

                $same = DB::table('rombels')->where('name', $name)->where('id', '!=', $rombel->id)->first();

                if ($same) {
                    DB::table('users')->where('rombel_id', $rombel->id)->update(['rombel_id' => $same->id]);
                    DB::table('classrooms')->where('rombel_id', $rombel->id)->update(['rombel_id' => $same->id]);
                    DB::table('rombels')->where('id', $rombel->id)->delete();
                } else {
                    DB::table('rombels')->where('id', $rombel->id)->update(['name' => $name, 'updated_at' => $now]);
                }
            }

            foreach (range(1, 6) as $grade) {
                foreach (['A', 'B'] as $letter) {
                    DB::table('rombels')->insertOrIgnore([
                        'name' => $grade.$letter,
                        'grade_level' => $grade,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        // Tidak dibalik: nama rombel lama dan rombel yang ditambah admin tidak bisa dibedakan lagi.
    }
};
