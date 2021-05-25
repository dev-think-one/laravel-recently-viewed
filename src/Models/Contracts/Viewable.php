<?php

namespace RecentlyViewed\Models\Contracts;

interface Viewable
{
    /**
     * @param array $values
     *
     * @return mixed
     */
    public function whereRecentlyViewedIn(array $values);

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName();

    /**
     * Get recently vuewed items count.
     *
     * @return int
     */
    public function getRecentlyViewsLimit(): int;
}
