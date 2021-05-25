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
     * @param array $values
     *
     * @return mixed
     */
    public function whereRecentlyViewedIn(array $values)
    {
        $values_ordered = implode(',', $values);

        return static::whereIn($this->getKeyName(), $values)
                     ->orderByRaw("FIELD({$this->getKeyName()}, {$values_ordered})");
    }

    public function getRecentlyViewsLimit(): int
    {
        return 10;
    }
}
