<?php

namespace RecentlyViewed\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use RecentlyViewed\Facades\RecentlyViewed;
use RecentlyViewed\Tests\Fixtures\Models\Post;
use RecentlyViewed\Tests\Fixtures\Models\Product;

class NotPersistentStorageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

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
        $this->assertIsArray($this->getSessionByKey($post1::class));
        $this->assertCount(1, $this->getSessionByKey($post1::class));
        $this->assertEquals($post1->getKey(), $this->getSessionByKey($post1::class.'.0'));

        RecentlyViewed::add($product);
        $this->assertCount(1, $this->getSessionByKey($post1::class));
        $this->assertEquals($post1->getKey(), $this->getSessionByKey($post1::class.'.0'));
        $this->assertIsArray($this->getSessionByKey($product::class));
        $this->assertCount(1, $this->getSessionByKey($product::class));
        $this->assertEquals($product->getKey(), $this->getSessionByKey($product::class.'.0'));

        RecentlyViewed::add($post);
        $this->assertCount(2, $this->getSessionByKey($post::class));
        $this->assertEquals($post->getKey(), $this->getSessionByKey($post::class.'.0'));
        $this->assertEquals($post1->getKey(),  $this->getSessionByKey($post::class.'.1'));
        $this->assertIsArray($this->getSessionByKey($product::class));
        $this->assertCount(1,  $this->getSessionByKey($product::class));
        $this->assertEquals($product->getKey(),  $this->getSessionByKey($product::class.'.0'));

        RecentlyViewed::add($product1);
        $this->assertCount(2, $this->getSessionByKey($post::class));
        $this->assertEquals($post->getKey(), $this->getSessionByKey($post::class.'.0'));
        $this->assertEquals($post1->getKey(),  $this->getSessionByKey($post::class.'.1'));
        $this->assertCount(2,  $this->getSessionByKey($product::class));
        $this->assertEquals($product1->getKey(),  $this->getSessionByKey($product::class.'.0'));
        $this->assertEquals($product->getKey(),  $this->getSessionByKey($product::class.'.1'));
    }

    /** @test */
    public function clear_by_viewable()
    {
        $this->flushSession();

        $post     = Post::factory()->create();
        $post1    = Post::factory()->create();
        $product  = Product::factory()->create();
        $product1 = Product::factory()->create();

        RecentlyViewed::add($post);
        RecentlyViewed::add($post1);
        RecentlyViewed::add($product);
        RecentlyViewed::add($product1);
        $this->assertCount(2, $this->getSessionByKey($post::class));
        $this->assertCount(2, $this->getSessionByKey($product::class));

        RecentlyViewed::clear($product);
        $this->assertCount(2, $this->getSessionByKey($post::class));
        $this->assertNull($this->getSessionByKey($product::class));

        RecentlyViewed::clear($post);
        $this->assertNull($this->getSessionByKey($post::class));
        $this->assertNull($this->getSessionByKey($product::class));
    }

    /** @test */
    public function clear_all()
    {
        $this->flushSession();

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
    }
}
