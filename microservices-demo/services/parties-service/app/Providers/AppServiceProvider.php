<?php

namespace App\Providers;

use App\Models\Party;
use App\Models\PartyRole;
use App\Observers\PartyObserver;
use App\Observers\PartyRoleObserver;
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
        Party::observe(PartyObserver::class);
        PartyRole::observe(PartyRoleObserver::class);
    }
}
