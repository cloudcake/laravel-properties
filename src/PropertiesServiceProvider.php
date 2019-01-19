<?php

namespace Properties;

use Illuminate\Support\ServiceProvider;

class PropertiesServiceProvider extends ServiceProvider
{
    /**
     * Boot up Shovel.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfigurations();
        $this->registerMigrations();
    }

    /**
     * Register Properties configs.
     *
     * @return void
     */
    private function registerConfigurations()
    {
        $this->publishes([
            __DIR__.'/Config/config.php' => config_path('properties.php'),
        ]);
    }

    /**
     * Register Properties migrations.
     *
     * @return void
     */
    private function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
    }
}
