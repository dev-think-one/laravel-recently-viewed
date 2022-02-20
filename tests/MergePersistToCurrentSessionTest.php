<?php

namespace RecentlyViewed\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use RecentlyViewed\Facades\RecentlyViewed;
use RecentlyViewed\PersistManager;
use RecentlyViewed\Tests\Fixtures\Models\Page;
use RecentlyViewed\Tests\Fixtures\Models\Post;
use RecentlyViewed\Tests\Fixtures\Models\Product;
use RecentlyViewed\Tests\Fixtures\Models\User;

class MergePersistToCurrentSessionTest extends TestCase
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

        // Login user
        $this->actingAs($this->user);
        $this->assertAuthenticatedAs($this->user);
        RecentlyViewed::add($post1);
        RecentlyViewed::add($product1);
        $this->assertCount(1, $this->getSessionByKey($post::class));
        $this->assertCount(1, $this->user->getRecentViews($post::class)->first());
        $this->assertCount(1, $this->getSessionByKey($product1::class));
        $this->assertCount(1, $this->user->getRecentViews($product1::class)->first());
        $this->assertNull($this->getSessionByKey(Page::class));
        $this->assertCount(0, $this->user->getRecentViews(Page::class));

        // Logout user
        $this->app['auth']->guard()->logout();
        $this->assertGuest();
        RecentlyViewed::add($post);
        RecentlyViewed::add($product);
        $this->assertCount(2, $this->getSessionByKey($post::class));
        $this->assertCount(1, $this->user->getRecentViews($post::class)->first());
        $this->assertCount(2, $this->getSessionByKey($product1::class));
        $this->assertCount(1, $this->user->getRecentViews($product1::class)->first());
        $this->assertNull($this->getSessionByKey(Page::class));
        $this->assertCount(0, $this->user->getRecentViews(Page::class));

        // Nothing changed if no current user
        RecentlyViewed::mergePersistToCurrentSession();
        $this->assertCount(2, $this->getSessionByKey($post::class));
        $this->assertCount(1, $this->user->getRecentViews($post::class)->first());
        $this->assertCount(2, $this->getSessionByKey($product1::class));
        $this->assertCount(1, $this->user->getRecentViews($product1::class)->first());

        // Add additional persisted values
        $post2    = Post::factory()->create();
        $product2 = Product::factory()->create();
        $page     = Page::factory()->create();
        $page1    = Page::factory()->create();
        $this->user->syncRecentViews($post::class, array_merge($this->user->getRecentViews($post::class)->first(), [$post2->getKey()]));
        $this->user->syncRecentViews($product::class, array_merge($this->user->getRecentViews($product::class)->first(), [$product2->getKey()]));
        $this->user->syncRecentViews($page::class, [$page->getKey(), $page1->getKey()]);
        $this->user->refresh();
        $this->assertCount(2, $this->getSessionByKey($post::class));
        $this->assertCount(2, $this->user->getRecentViews($post::class)->first());
        $this->assertCount(2, $this->getSessionByKey($product::class));
        $this->assertCount(2, $this->user->getRecentViews($product::class)->first());
        $this->assertNull($this->getSessionByKey(Page::class));
        $this->assertCount(2, $this->user->getRecentViews(Page::class)->first());

        // Login again
        $this->actingAs($this->user);
        $this->assertAuthenticatedAs($this->user);
        RecentlyViewed::mergePersistToCurrentSession();
        $this->assertCount(3, $this->getSessionByKey($post::class));
        $this->assertCount(3, $this->user->getRecentViews($post::class)->first());
        $this->assertCount(3, $this->getSessionByKey($product1::class));
        $this->assertCount(3, $this->user->getRecentViews($product1::class)->first());
        $this->assertCount(2, $this->getSessionByKey($page::class));
        $this->assertCount(2, $this->user->getRecentViews($page::class)->first());
    }
}
