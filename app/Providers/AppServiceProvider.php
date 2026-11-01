<?php

namespace App\Providers;

use App\Models\Material;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        ]);
    }
}
