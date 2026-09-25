<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;
use App\Models\Company;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Paginator::useBootstrapFive();

        try {
            // Ambil data pertama Company sebagai pengaturan global dan Cache selama 24 jam (1440 menit)
            // agar tidak query ke database setiap kali buka halaman
            $siteSettings = Cache::remember('global_site_settings', 1440, function () {
                return Company::first();
            });

            // Bagikan variabel $siteSettings ke semua file Blade
            View::share('siteSettings', $siteSettings);

        } catch (\Exception $e) {
            // Try-catch digunakan agar saat pertama kali 'php artisan migrate' (tabel belum ada) tidak terjadi error
            View::share('siteSettings', null);
        }
    }
}