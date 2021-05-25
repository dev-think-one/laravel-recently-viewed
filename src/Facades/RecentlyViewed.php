<?php

namespace RecentlyViewed\Facades;

use Illuminate\Support\Facades\Facade;
use RecentlyViewed\Models\Contracts\Viewable;

/**
 * Class RecentlyViewed
 * @package RecentlyViewed\Facades
 *
 * @method static \RecentlyViewed\RecentlyViewed add(Viewable $viewable)
 * @method static \Illuminate\Database\Eloquent\Builder|null getQuery($viewable)
 * @method static \Illuminate\Support\Collection get($viewable)
 * @method static \RecentlyViewed\RecentlyViewed clear($viewable)
 * @method static \RecentlyViewed\RecentlyViewed clearAll()
 */
class RecentlyViewed extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \RecentlyViewed\RecentlyViewed::class;
    }
}
