<?php

namespace RecentlyViewed\Tests;

use RecentlyViewed\PersistManager;

class PersistManagerTest extends TestCase
{
    /** @test */
    public function runs_migrations()
    {
        $this->assertFalse(PersistManager::$runsMigrations);
        PersistManager::enableMigrations();
        $this->assertTrue(PersistManager::$runsMigrations);
    }
}
