<?php

namespace RecentlyViewed;

use Illuminate\Database\Query\Builder;
use RecentlyViewed\Exceptions\ShouldBeViewableException;
use RecentlyViewed\Models\Contracts\Viewable;

class RecentlyViewed
{
    /**
     * @param Viewable $viewable
     * @return $this
     */
    public function add(Viewable $viewable)
    {
        $keys = session()->get(config('recently-viewed.session_prefix') . '.' . get_class($viewable));
        if (! is_array($keys)) {
            $keys = [];
        }
        array_unshift($keys, $viewable->getKey());
        session()->put(
            config('recently-viewed.session_prefix') . '.' . get_class($viewable),
            array_slice(array_unique($keys), 0, $viewable->getRecentlyViewsLimit())
        );

        return $this;
    }

    /**
     * @param Viewable|string $viewable
     * @return mixed
     * @throws ShouldBeViewableException
     */
    public function getQuery($viewable)
    {
        if (! ($viewable instanceof Viewable) && is_string($viewable)) {
            $viewable = new $viewable();
        }
        if (! ($viewable instanceof Viewable)) {
            throw new ShouldBeViewableException('Entity should implement Viewable interface');
        }

        $keys = session()->get(config('recently-viewed.session_prefix') . '.' . get_class($viewable));

        return $viewable->whereRecentlyViewedIn($keys);
    }

    /**
     * @param Viewable|string $viewable
     * @param int|null $limit
     * @return \Illuminate\Support\Collection
     * @throws ShouldBeViewableException
     */
    public function get($viewable, int $limit = null): \Illuminate\Support\Collection
    {
        if (! ($viewable instanceof Viewable) && is_string($viewable)) {
            $viewable = new $viewable();
        }
        if (! ($viewable instanceof Viewable)) {
            throw new ShouldBeViewableException('Entity should implement Viewable interface');
        }

        $query = $this->getQuery($viewable);
        if (($query instanceof Builder) || ($query instanceof \Illuminate\Database\Eloquent\Builder)) {
            /** * @psalm-suppress InvalidReturnStatement */
            return $query->take($limit ?? $viewable->getRecentlyViewsLimit())->get();
        }
        if ($query instanceof \Illuminate\Support\Collection) {
            return $query;
        }
        if (method_exists($viewable, 'makeQuery')) {
            return $viewable->makeQuery($query);
        }

        return collect([]);
    }

    /**
     * @param Viewable|string $viewable
     * @return RecentlyViewed
     * @throws ShouldBeViewableException
     */
    public function clear($viewable): RecentlyViewed
    {
        if (! ($viewable instanceof Viewable) && is_string($viewable)) {
            $viewable = new $viewable();
        }
        if (! ($viewable instanceof Viewable)) {
            throw new ShouldBeViewableException('Entity should implement Viewable interface');
        }

        session()->forget(config('recently-viewed.session_prefix') . '.' . get_class($viewable));

        return $this;
    }

    /**
     * @return RecentlyViewed
     */
    public function clearAll(): RecentlyViewed
    {
        session()->forget(config('recently-viewed.session_prefix'));

        return $this;
    }
}
