<?php

namespace App\Providers;

use App\Models\SecurityEvent;
use App\Services\AlertService;
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
        SecurityEvent::created(function (SecurityEvent $event) {
            if (in_array($event->threat_level, ['HIGH', 'CRITICAL'])) {
                AlertService::sendSecurityAlert($event);
            }
        });
    }
}
