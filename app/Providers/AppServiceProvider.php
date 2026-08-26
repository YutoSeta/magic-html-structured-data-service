<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\RateLimiter;
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
        JsonResource::withoutWrapping();
        RateLimiter::for('structured-data-writes', fn (Request $request): Limit => Limit::perMinute((int) config('structured_data.writes_per_minute'))
            ->by((string) $request->bearerToken()));
        RateLimiter::for('structured-data-reads', fn (Request $request): Limit => Limit::perMinute((int) config('structured_data.reads_per_minute'))
            ->by((string) $request->bearerToken()));
    }
}
