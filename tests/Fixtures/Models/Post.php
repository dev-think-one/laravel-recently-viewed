<?php

namespace RecentlyViewed\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RecentlyViewed\Models\Contracts\Viewable;
use RecentlyViewed\Models\Traits\CanBeViewed;
use RecentlyViewed\Tests\Fixtures\Factories\PostFactory;

class Post extends Model implements Viewable
{
    use CanBeViewed, HasFactory;

    protected $table = 'test_posts';

    protected $guarded = [];

    protected static function newFactory()
    {
        return new PostFactory();
    }
}
