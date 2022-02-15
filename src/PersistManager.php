<?php

namespace RecentlyViewed;

class PersistManager
{
    /**
     * Indicates if laravel should run migrations for package.
     *
     * @var bool
     */
    public static bool $runsMigrations = false;

    /**
     * UsedContactModel.
     *
     * @var string
     */
    public static string $model = \RecentlyViewed\Models\RecentViews::class;

    /**
     * Check is persistent storage enabled.
     *
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return (bool) config('recently-viewed.persist_enabled');
    }
    /**
     * Configure laravel to not register current package migrations.
     *
     * @return static
     */
    public static function enableMigrations(): static
    {
        static::$runsMigrations = true;

        return new static;
    }

    /**
     * Specify contact model to use.
     *
     * @param  string  $modelClass
     * @return static
     */
    public static function useRecentlyViewedModel(string $modelClass): static
    {
        static::$model = $modelClass;

        return new static;
    }
}
