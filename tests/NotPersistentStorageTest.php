<?php

namespace RecentlyViewed\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

class NotPersistentStorageTest extends TestCase
{
    use RefreshDatabase;

    protected string $sessionPrefix;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sessionPrefix = config('recently-viewed.session_prefix');
    }

    /** @test */
    public function add_to_recently_viewed()
    {
        $this->flushSession();

        $post = \RecentlyViewed\Tests\Fixtures\Models\Post::fake();
        $post->save();
        $post1 = \RecentlyViewed\Tests\Fixtures\Models\Post::fake();
        $post1->save();
        $product = \RecentlyViewed\Tests\Fixtures\Models\Product::fake();
        $product->save();
        $product1 = \RecentlyViewed\Tests\Fixtures\Models\Product::fake();
        $product1->save();

        $this->assertEmpty($this->app['session']->get("{$this->sessionPrefix}.".$post1::class));

        \RecentlyViewed\Facades\RecentlyViewed::add($post1);
        $this->assertIsArray($this->app['session']->get("{$this->sessionPrefix}.".$post1::class));
        $this->assertCount(1, $this->app['session']->get("{$this->sessionPrefix}.".$post1::class));
        $this->assertEquals($post1->getKey(), $this->app['session']->get("{$this->sessionPrefix}.".$post1::class . '.0'));

        \RecentlyViewed\Facades\RecentlyViewed::add($product);
        $this->assertCount(1, $this->app['session']->get("{$this->sessionPrefix}.".$post::class));
        $this->assertEquals($post1->getKey(), $this->app['session']->get("{$this->sessionPrefix}.".$post::class . '.0'));
        $this->assertIsArray($this->app['session']->get("{$this->sessionPrefix}.".$product::class));
        $this->assertCount(1, $this->app['session']->get("{$this->sessionPrefix}.".$product::class));
        $this->assertEquals($product->getKey(), $this->app['session']->get("{$this->sessionPrefix}.".$product::class . '.0'));

        \RecentlyViewed\Facades\RecentlyViewed::add($post);
        $this->assertCount(2, $this->app['session']->get("{$this->sessionPrefix}.".$post::class));
        $this->assertEquals($post->getKey(), $this->app['session']->get("{$this->sessionPrefix}.".$post::class . '.0'));
        $this->assertEquals($post1->getKey(), $this->app['session']->get("{$this->sessionPrefix}.".$post::class . '.1'));
        $this->assertIsArray($this->app['session']->get("{$this->sessionPrefix}.".$product::class));
        $this->assertCount(1, $this->app['session']->get("{$this->sessionPrefix}.".$product::class));
        $this->assertEquals($product->getKey(), $this->app['session']->get("{$this->sessionPrefix}.".$product::class . '.0'));

        \RecentlyViewed\Facades\RecentlyViewed::add($product1);
        $this->assertCount(2, $this->app['session']->get("{$this->sessionPrefix}.".$post::class));
        $this->assertEquals($post->getKey(), $this->app['session']->get("{$this->sessionPrefix}.".$post::class . '.0'));
        $this->assertEquals($post1->getKey(), $this->app['session']->get("{$this->sessionPrefix}.".$post::class . '.1'));
        $this->assertCount(2, $this->app['session']->get("{$this->sessionPrefix}.".$product::class));
        $this->assertEquals($product1->getKey(), $this->app['session']->get("{$this->sessionPrefix}.".$product::class . '.0'));
        $this->assertEquals($product->getKey(), $this->app['session']->get("{$this->sessionPrefix}.".$product::class . '.1'));
    }

    /** @test */
    public function clear_by_viewable()
    {
        $this->flushSession();

        $post = \RecentlyViewed\Tests\Fixtures\Models\Post::fake();
        $post->save();
        $post1 = \RecentlyViewed\Tests\Fixtures\Models\Post::fake();
        $post1->save();
        $product = \RecentlyViewed\Tests\Fixtures\Models\Product::fake();
        $product->save();
        $product1 = \RecentlyViewed\Tests\Fixtures\Models\Product::fake();
        $product1->save();

        \RecentlyViewed\Facades\RecentlyViewed::add($post);
        \RecentlyViewed\Facades\RecentlyViewed::add($post1);
        \RecentlyViewed\Facades\RecentlyViewed::add($product);
        \RecentlyViewed\Facades\RecentlyViewed::add($product1);
        $this->assertCount(2, $this->app['session']->get("{$this->sessionPrefix}.".$post::class));
        $this->assertCount(2, $this->app['session']->get("{$this->sessionPrefix}.".$product::class));

        \RecentlyViewed\Facades\RecentlyViewed::clear($product);
        $this->assertCount(2, $this->app['session']->get("{$this->sessionPrefix}.".$post::class));
        $this->assertNull($this->app['session']->get("{$this->sessionPrefix}.".$product::class));

        \RecentlyViewed\Facades\RecentlyViewed::clear($post);
        $this->assertNull($this->app['session']->get("{$this->sessionPrefix}.".$post::class));
        $this->assertNull($this->app['session']->get("{$this->sessionPrefix}.".$product::class));
    }

    /** @test */
    public function clear_all()
    {
        $this->flushSession();

        $post = \RecentlyViewed\Tests\Fixtures\Models\Post::fake();
        $post->save();
        $post1 = \RecentlyViewed\Tests\Fixtures\Models\Post::fake();
        $post1->save();
        $product = \RecentlyViewed\Tests\Fixtures\Models\Product::fake();
        $product->save();
        $product1 = \RecentlyViewed\Tests\Fixtures\Models\Product::fake();
        $product1->save();


        \RecentlyViewed\Facades\RecentlyViewed::clearAll();
        $this->assertNull($this->app['session']->get("{$this->sessionPrefix}.".$post::class));
        $this->assertNull($this->app['session']->get("{$this->sessionPrefix}.".$product::class));
    }
}
