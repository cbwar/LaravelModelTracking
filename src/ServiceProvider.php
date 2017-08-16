<?php

namespace Cbwar\Laravel\ModelChanges;

use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{

    private function basePath()
    {
        return __DIR__ . '/..';
    }

    private function configPath()
    {
        return $this->basePath() . '/config/modelchanges.php';
    }

    public function register()
    {


    }

    public function boot()
    {
        // Migrations
        $this->loadMigrationsFrom($this->basePath() . '/migrations');

        // Translations
        $this->loadTranslationsFrom($this->basePath() . '/resources/lang', 'modelchanges');

        // Config
        $this->publishes([$this->configPath() => config_path('modelchanges.php')], 'config');
    }

}