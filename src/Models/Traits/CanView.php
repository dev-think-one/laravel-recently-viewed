<?php


namespace RecentlyViewed\Models\Traits;

trait CanView
{
    public function deleteRecentViews(array $types = [])
    {
        if (empty($types)) {
            return $this->recentViews()->delete();
        }

        return $this->recentViews()->whereIn('type', $types)->delete();
    }

    public function getRecentViews(array $types = [])
    {
        if (empty($types)) {
            return $this->recentViews()->get()->pluck('views', 'type')->map(fn ($views) => json_decode($views, true));
        }

        return $this->recentViews()->whereIn('type', $types)->get()->pluck('views', 'type');
    }

    public function syncRecentViews(string $type, array $data = [])
    {
        return $this->recentViews()->updateOrCreate(['type' => $type], ['views' => json_encode($data)]);
    }

    public function recentViews()
    {
        return $this->morphMany(config('recently-viewed.persist_model'), 'viewer');
    }
}
