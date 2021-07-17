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

            if (!class_exists('CreateRecentViewsTables')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_recent_views_tables.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_recent_views_tables.php'),
                ], 'migrations');
            }

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
