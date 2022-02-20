<?php

namespace RecentlyViewed\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use RecentlyViewed\Facades\RecentlyViewed;
use RecentlyViewed\PersistManager;
use RecentlyViewed\Tests\Fixtures\Models\Country;
use RecentlyViewed\Tests\Fixtures\Models\User;
use RecentlyViewed\Tests\Fixtures\Models\UUIDRecentViews;

class UUIDPersistentStorageTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        PersistManager::enableMigrations();
        PersistManager::useRecentlyViewedModel(UUIDRecentViews::class);
        parent::setUp();

        $this->app['config']->set('recently-viewed.persist_enabled', true);

        $this->user = User::factory()->create();
        Country::factory()->count(rand(10, 100))->create();
    }

    /** @test */
    public function add_to_recently_viewed()
    {
        $this->flushSession();

        $country     = Country::factory()->create();
        $country1    = Country::factory()->create();

        $this->assertEmpty($this->getSessionByKey($country1::class));

        RecentlyViewed::add($country1);
        $this->assertEquals($country1->getKey(), $this->getSessionByKey($country1::class.'.0'));
        $this->assertNull($this->user->getRecentViews($country1::class)->first());

        $this->actingAs($this->user);

        RecentlyViewed::add($country1);
        $this->assertEquals($country1->getKey(), $this->getSessionByKey($country1::class.'.0'));
        $this->assertCount(1, $this->user->getRecentViews($country1::class)->first());
        $this->assertTrue(in_array($country1->getKey(), $this->user->getRecentViews($country1::class)->first()));

        RecentlyViewed::add($country);
        $this->assertEquals($country->getKey(), $this->getSessionByKey($country::class.'.0'));
        $this->assertEquals($country1->getKey(), $this->getSessionByKey($country::class.'.1'));
        $this->assertCount(2, $this->user->getRecentViews($country::class)->first());
        $this->assertTrue(in_array($country1->getKey(), $this->user->getRecentViews($country::class)->first()));
        $this->assertTrue(in_array($country->getKey(), $this->user->getRecentViews($country::class)->first()));
    }
}
