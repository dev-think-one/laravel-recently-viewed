<?php

namespace RecentlyViewed\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

class NotPersistentStorageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function runs_migrations()
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

        $this->assertEmpty($this->app['session']->get(config('recently-viewed.session_prefix').'.'.$post1::class));

        \RecentlyViewed\Facades\RecentlyViewed::add($post1);
        $this->assertIsArray($this->app['session']->get(config('recently-viewed.session_prefix').'.'.$post1::class));
        $this->assertCount(1, $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$post1::class));
        $this->assertEquals($post1->getKey(), $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$post1::class . '.0'));

        \RecentlyViewed\Facades\RecentlyViewed::add($product);
        $this->assertCount(1, $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$post::class));
        $this->assertEquals($post1->getKey(), $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$post::class . '.0'));
        $this->assertIsArray($this->app['session']->get(config('recently-viewed.session_prefix').'.'.$product::class));
        $this->assertCount(1, $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$product::class));
        $this->assertEquals($product->getKey(), $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$product::class . '.0'));

        \RecentlyViewed\Facades\RecentlyViewed::add($post);
        $this->assertCount(2, $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$post::class));
        // Added in reverse order
        $this->assertEquals($post->getKey(), $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$post::class . '.0'));
        $this->assertEquals($post1->getKey(), $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$post::class . '.1'));
        $this->assertIsArray($this->app['session']->get(config('recently-viewed.session_prefix').'.'.$product::class));
        $this->assertCount(1, $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$product::class));
        $this->assertEquals($product->getKey(), $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$product::class . '.0'));

        \RecentlyViewed\Facades\RecentlyViewed::add($product1);
        $this->assertCount(2, $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$post::class));
        // Added in reverse order
        $this->assertEquals($post->getKey(), $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$post::class . '.0'));
        $this->assertEquals($post1->getKey(), $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$post::class . '.1'));
        $this->assertCount(2, $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$product::class));
        $this->assertEquals($product1->getKey(), $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$product::class . '.0'));
        $this->assertEquals($product->getKey(), $this->app['session']->get(config('recently-viewed.session_prefix').'.'.$product::class . '.1'));
    }
}
