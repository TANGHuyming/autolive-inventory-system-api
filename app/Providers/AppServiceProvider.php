<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;

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
        //
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->response(function (Request $request) {
                return response()->json([
                    "success" => false,
                    "data" => [],
                    "message" => "Too many requests",
                ], 429);
            });
        });

        RateLimiter::for("login", function (Request $request) {
            $throttle_key = $request->ip();

            return Limit::perMinute(5)->by($throttle_key)->response(function (Request $request) {
                return response()->json([
                    "success" => false,
                    "data" => [],
                    "message" => "Too many attempts",
                ]);
            });
        });

        // Log Cache Hits
        Event::listen(CacheHit::class, function (CacheHit $event) {
            Log::info("Cache HIT", [
                'key' => $event->key,
                'tags' => $event->tags ?? [],
            ]);
        });

        // Log Cache Misses
        Event::listen(CacheMissed::class, function (CacheMissed $event) {
            Log::warning("Cache MISS", [
                'key' => $event->key,
                'tags' => $event->tags ?? [],
            ]);
        });
    }
}
