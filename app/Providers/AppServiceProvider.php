<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // [Tambahkan Import Ini]

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
        // [Tambahkan Kode Ini]
        // Memaksa schema HTTPS jika aplikasi berjalan di environment production atau remote
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}