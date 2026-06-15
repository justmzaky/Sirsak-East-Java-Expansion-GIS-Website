<?php

namespace App\Providers;

use App\Models\Collection;
use App\Models\Shipment;
use App\Observers\CollectionObserver;
use App\Observers\ShipmentObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Collection::observe(CollectionObserver::class);
        Shipment::observe(ShipmentObserver::class);

        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
