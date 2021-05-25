<?php

namespace RecentlyViewed\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use RecentlyViewed\ServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
    }
}
