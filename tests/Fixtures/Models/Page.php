<?php

namespace RecentlyViewed\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RecentlyViewed\Models\Contracts\Viewable;
use RecentlyViewed\Models\Traits\CanBeViewed;
use RecentlyViewed\Tests\Fixtures\Factories\PageFactory;

class Page extends Model implements Viewable
{
    use CanBeViewed, HasFactory;

    protected $table = 'test_pages';

    protected $guarded = [];

    protected static function newFactory()
    {
        return new PageFactory();
    }
}
