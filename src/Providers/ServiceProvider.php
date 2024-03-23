<?php

namespace Ja\Shipping\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Ja\Shipping\Support\Carrier;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->app->singleton('carrier', fn () => new Carrier);
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
    }
}
