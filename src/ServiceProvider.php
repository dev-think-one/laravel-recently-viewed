<?php

namespace RecentlyViewed;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/recently-viewed.php' => config_path('recently-viewed.php'),
            ], 'config');

            $this->commands([
            ]);

            $this->registerMigrations();
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'recently-viewed');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/recently-viewed.php', 'recently-viewed');
    }

    /**
     * Register the package migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (PersistManager::$runsMigrations) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }
}
