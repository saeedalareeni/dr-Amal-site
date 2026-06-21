<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;

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
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by(strtolower((string) $request->email).'|'.$request->ip()));
        RateLimiter::for('contact', fn (Request $request) => [Limit::perMinute(5)->by($request->ip()), Limit::perDay(15)->by(strtolower((string) $request->email))]);
        RateLimiter::for('newsletter', fn (Request $request) => [Limit::perHour(3)->by($request->ip()), Limit::perDay(5)->by(strtolower((string) $request->email))]);
    }
}
