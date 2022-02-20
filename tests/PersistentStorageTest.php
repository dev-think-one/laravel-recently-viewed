<?php

namespace RecentlyViewed\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use RecentlyViewed\Exceptions\ShouldBeViewableException;
use RecentlyViewed\Facades\RecentlyViewed;
use RecentlyViewed\PersistManager;
use RecentlyViewed\Tests\Fixtures\Models\Post;
use RecentlyViewed\Tests\Fixtures\Models\PostNotViewable;
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

    /** @test */
    public function clear_by_viewable()
    {
        $this->flushSession();
        $this->actingAs($this->user);
        $post     = Post::factory()->create();
        $post1    = Post::factory()->create();
        $product  = Product::factory()->create();
        $product1 = Product::factory()->create();

        RecentlyViewed::add($post);
        RecentlyViewed::add($post1);
        RecentlyViewed::add($product);
        RecentlyViewed::add($product1);
        $this->assertCount(2, $this->getSessionByKey($post::class));
        $this->assertCount(2, $this->user->getRecentViews($post::class)->first());
        $this->assertCount(2, $this->getSessionByKey($product::class));
        $this->assertCount(2, $this->user->getRecentViews($product::class)->first());

        RecentlyViewed::clear($product);
        $this->assertCount(2, $this->getSessionByKey($post::class));
        $this->assertCount(2, $this->user->getRecentViews($post::class)->first());
        $this->assertNull($this->getSessionByKey($product::class));
        $this->assertCount(0, $this->user->getRecentViews($product::class));

        RecentlyViewed::clear($post::class);
        $this->assertNull($this->getSessionByKey($post::class));
        $this->assertCount(0, $this->user->getRecentViews($product::class));
        $this->assertNull($this->getSessionByKey($product::class));
        $this->assertCount(0, $this->user->getRecentViews($product::class));
        $this->assertCount(0, $this->user->getRecentViews());
    }

    /** @test */
    public function clear_by_viewable_error_if_class_not_viewable()
    {
        $this->flushSession();
        $this->actingAs($this->user);

        RecentlyViewed::clear(Post::class);
        RecentlyViewed::clear(Product::class);

        $this->expectException(ShouldBeViewableException::class);

        RecentlyViewed::clear(PostNotViewable::class);
    }

    /** @test */
    public function clear_all()
    {
        $this->flushSession();
        $this->actingAs($this->user);
        $post     = Post::factory()->create();
        $post1    = Post::factory()->create();
        $product  = Product::factory()->create();
        $product1 = Product::factory()->create();

        RecentlyViewed::add($post);
        RecentlyViewed::add($post1);
        RecentlyViewed::add($product);
        RecentlyViewed::add($product1);

        RecentlyViewed::clearAll();
        $this->assertNull($this->getSessionByKey($post::class));
        $this->assertNull($this->getSessionByKey($product::class));
        $this->assertCount(0, $this->user->getRecentViews());
    }

    /** @test */
    public function get_by_type()
    {
        $this->flushSession();
        $this->actingAs($this->user);
        $post  = Post::factory()->create();
        $post1 = Post::factory()->create();

        RecentlyViewed::add($post1);
        RecentlyViewed::add($post);

        $posts    = RecentlyViewed::get($post);
        $products = RecentlyViewed::get(Product::class);

        $this->assertCount(2, $posts);
        $this->assertInstanceOf(Collection::class, $posts);
        $this->assertCount(0, $products);
        $this->assertInstanceOf(Collection::class, $products);

        $this->assertTrue($post1->getKey() > $post->getKey());
        $this->assertEquals($post->getKey(), $posts->first()->getKey());
        $this->assertEquals($post1->getKey(), $posts->get(1)->getKey());
    }
}
