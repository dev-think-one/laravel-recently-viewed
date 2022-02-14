<?php

namespace RecentlyViewed\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use RecentlyViewed\Models\Contracts\Viewable;
use RecentlyViewed\Models\Traits\CanBeViewed;

class Product extends Model implements Viewable
{
    use CanBeViewed;

    protected $table = 'test_products';

    protected $guarded = [];

    public static function fake(array $data = []): static
    {
        $instance = new static(array_merge($data, [
            'title' => 'Post ' . rand(),
        ]));

        return $instance;
    }
}
