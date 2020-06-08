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

            $this->publishes([
                __DIR__ . '/../resources/views' => base_path('resources/views/vendor/recently-viewed'),
            ], 'views');


            $this->commands([
            ]);
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'recently-viewed');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/recently-viewed.php', 'recently-viewed');
    }
}
