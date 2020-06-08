<?php

namespace RecentlyViewed\Facades;

use Illuminate\Support\Facades\Facade;
use RecentlyViewed\Models\Contracts\Viewable;

/**
 * Class RecentlyViewed
 * @package RecentlyViewed\Facades
 *
 * @method static \RecentlyViewed\RecentlyViewed add(Viewable $viewable)
 * @method static mixed getQuery($viewable)
 * @method static \Illuminate\Support\Collection get($viewable)
 */
class RecentlyViewed extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \RecentlyViewed\RecentlyViewed::class;
    }
}
