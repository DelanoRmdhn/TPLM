<?php

namespace App\Providers;

use App\Models\Assignment;
use App\Models\Material;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Nama tetap untuk kolom attachable_type, supaya data tidak rusak kalau class dipindah.
        Relation::enforceMorphMap([
            'material' => Material::class,
            'assignment' => Assignment::class,
            'user' => User::class, // notifications.notifiable_type
        ]);

        // Jumlah notifikasi belum dibaca untuk tombol Notifikasi di navigasi
        View::composer(
            ['layouts.sementara', 'notifikasi.index', 'guru.beranda', 'siswa.beranda', 'siswa.tugas'],
            fn ($view) => $view->with('unreadNotifications', auth()->user()?->unreadNotifications()->count() ?? 0),
        );
    }
}
