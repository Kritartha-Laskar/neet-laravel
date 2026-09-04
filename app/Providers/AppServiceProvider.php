<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
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
        // Fix for MySQL older versions: "Specified key was too long; max key length is 1000 bytes"
        Schema::defaultStringLength(191);

        // Use Bootstrap 5 styling for all pagination links (prevents unstyled huge SVG arrows)
        Paginator::useBootstrapFive();
    }
}
