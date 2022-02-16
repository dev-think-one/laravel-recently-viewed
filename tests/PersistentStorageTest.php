<?php

namespace RecentlyViewed\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use RecentlyViewed\Facades\RecentlyViewed;
use RecentlyViewed\PersistManager;
use RecentlyViewed\Tests\Fixtures\Models\Post;
use RecentlyViewed\Tests\Fixtures\Models\Product;
use RecentlyViewed\Tests\Fixtures\Models\User;

class PersistentStorageTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        PersistManager::enableMigrations();
        parent::setUp();

        $this->app['config']->set('recently-viewed.persist_enabled', true);

        $this->user = User::factory()->create();
        Post::factory()->count(rand(10, 100))->create();
        Product::factory()->count(rand(10, 100))->create();
    }

    protected function getSessionByKey(string $key)
    {
        $sessionPrefix = config('recently-viewed.session_prefix');

        return $this->app['session']->get("{$sessionPrefix}.".$key);
    }

    /** @test */
    public function add_to_recently_viewed()
    {
        $this->flushSession();


        $post     = Post::factory()->create();
        $post1    = Post::factory()->create();
        $product  = Product::factory()->create();
        $product1 = Product::factory()->create();

        $this->assertEmpty($this->getSessionByKey($post1::class));

        RecentlyViewed::add($post1);
        $this->assertEquals($post1->getKey(), $this->getSessionByKey($post::class.'.0'));
        $this->assertNull($this->user->getRecentViews($post::class)->first());

        $this->actingAs($this->user);

        RecentlyViewed::add($post1);
        $this->assertEquals($post1->getKey(), $this->getSessionByKey($post::class.'.0'));
        $this->assertCount(1, $this->user->getRecentViews($post::class)->first());
        $this->assertTrue(in_array($post1->getKey(), $this->user->getRecentViews($post::class)->first()));

        RecentlyViewed::add($post1);
        $this->assertEquals($post1->getKey(), $this->getSessionByKey($post::class.'.0'));
        $this->assertCount(1, $this->user->getRecentViews($post::class)->first());
        $this->assertTrue(in_array($post1->getKey(), $this->user->getRecentViews($post::class)->first()));

        RecentlyViewed::add($product);
        $this->assertEquals($post1->getKey(), $this->getSessionByKey($post::class.'.0'));
        $this->assertEquals($product->getKey(), $this->getSessionByKey($product::class.'.0'));
        $this->assertCount(1, $this->user->getRecentViews($post::class)->first());
        $this->assertTrue(in_array($post1->getKey(), $this->user->getRecentViews($post::class)->first()));
        $this->assertCount(1, $this->user->getRecentViews($product::class)->first());
        $this->assertTrue(in_array($product->getKey(), $this->user->getRecentViews($product::class)->first()));

        RecentlyViewed::add($post);
        RecentlyViewed::add($product1);
        $this->assertEquals($post->getKey(), $this->getSessionByKey($post::class.'.0'));
        $this->assertEquals($post1->getKey(), $this->getSessionByKey($post::class.'.1'));
        $this->assertCount(2, $this->user->getRecentViews($post::class)->first());
        $this->assertTrue(in_array($post->getKey(), $this->user->getRecentViews($post::class)->first()));
        $this->assertTrue(in_array($post1->getKey(), $this->user->getRecentViews($post::class)->first()));
        $this->assertCount(2, $this->user->getRecentViews($product::class)->first());
    }
}
