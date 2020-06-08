<?php

namespace RecentlyViewed\Models\Traits;

/**
 * Trait CanBeViewed
 * @package RecentlyViewed\Models\Traits
 *
 */
trait CanBeViewed
{

    /**
     * @param  mixed  $values
     * @return mixed
     */
    public function whereRecentlyViewedIn($values)
    {
        return  static::whereIn($this->getKeyName(), $values);
    }

    public function getRecentlyViewsLimit(): int
    {
        return 10;
    }
}
