<?php

namespace RecentlyViewed\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RecentlyViewed\Tests\Fixtures\Factories\PostFactory;

class PostNotViewable extends Model
{
    use HasFactory;

    protected $table = 'test_posts';

    protected $guarded = [];

    protected static function newFactory()
    {
        return new PostFactory();
    }
}
