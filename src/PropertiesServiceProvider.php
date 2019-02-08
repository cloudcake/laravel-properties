<?php

namespace Properties;

use Illuminate\Support\ServiceProvider;

class PropertiesServiceProvider extends ServiceProvider
{
    /**
     * Boot up Properties.
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
        ], 'config');
    }

    /**
     * Register Properties migrations.
     *
     * @return void
     */
    private function registerMigrations()
    {
        $this->publishes([
            __DIR__.'/Migrations' => database_path('migrations'),
        ], 'migrations');
        
        $this->publishes([
            __DIR__.'/MigrationsUuid' => database_path('migrations'),
        ], 'migrations-uuid');

    }
}
