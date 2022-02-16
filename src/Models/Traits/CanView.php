<?php

namespace RecentlyViewed\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use RecentlyViewed\PersistManager;

trait CanView
{
    public function deleteRecentViews(array|string|null $types = null)
    {
        if (is_null($types)) {
            return $this->recentViews()->delete();
        }

        return $this->recentViews()->whereIn('type', (array) $types)->delete();
    }

    public function getRecentViews(array|string|null $types = null): Collection
    {
        $query = $this->recentViews();
        if (!is_null($types)) {
            $query->whereIn('type', (array) $types);
        }

        return $query->get()->pluck('views', 'type')->map(fn ($views) => json_decode($views, true));
    }

    public function syncRecentViews(string $type, array $data = []): Model
    {
        $data = array_filter($data, fn ($i) => !empty($i) && (is_string($i) || is_integer($i)));

        return $this->recentViews()->updateOrCreate(['type' => $type], ['views' => json_encode($data)]);
    }

    public function recentViews(): MorphMany
    {
        return $this->morphMany(PersistManager::$model, 'viewer');
    }
}
