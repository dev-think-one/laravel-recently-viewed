<?php

namespace RecentlyViewed\Models\Contracts;

interface Viewable
{
    /**
     * @param  mixed  $values
     * @return mixed
     */
    public function whereRecentlyViewedIn($values);

    public function getKey();

    public function getRecentlyViewsLimit(): int ;
}
