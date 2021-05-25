<?php

namespace RecentlyViewed\Models\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Viewable
{
    /**
     * @param array $values
     *
     * @return null|Builder
     */
    public function whereRecentlyViewedIn(array $values): ?Builder;

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName();

    /**
     * Get recently viewed items count.
     *
     * @return int
     */
    public function getRecentlyViewsLimit(): int;
}
