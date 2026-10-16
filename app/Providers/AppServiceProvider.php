<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Rate limiter for public billing search (Anti-Scraping: 10 requests per minute per IP)
        RateLimiter::for('public-billings', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Rate limiter for authenticated student billing portals (20 requests per minute)
        RateLimiter::for('sensitive-portal', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
        });
    }
}