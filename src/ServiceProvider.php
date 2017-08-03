<?php

namespace Cbwar\Laravel\ModelChanges;

use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{

    public function register()
    {


    }

    public function boot()
    {
        // Migrations
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        // Translations
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'modelchanges');

        // Config
        $this->publishes([__DIR__ . '/config/modelchanges.php' => config_path('modelchanges.php')], 'config');
    }

}