<?php

namespace Cbwar\Laravel\ModelTracking;

use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{

    public function register()
    {


    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
    }

}