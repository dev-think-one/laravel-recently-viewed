<?php

namespace RecentlyViewed\Models\Contracts;

interface Viewer
{
    public function getRecentViews(array $types = []);

    public function deleteRecentViews(array $types = []);

    public function syncRecentViews(string $type, array $data = []);
}
