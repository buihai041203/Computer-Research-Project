<?php

namespace App\Providers;

use App\Models\SecurityEvent;
use App\Models\Domain;
use App\Policies\DomainPolicy;
use App\Services\AlertService;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(Domain::class, DomainPolicy::class);

        // Alerting handled in queued jobs for scaling; leave event observer minimal.
    }
}
