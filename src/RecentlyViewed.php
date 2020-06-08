<?php

namespace RecentlyViewed;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use RecentlyViewed\Exceptions\ShouldBeViewableException;
use RecentlyViewed\Models\Contracts\Viewable;
use RecentlyViewed\Models\Contracts\Viewer;

class RecentlyViewed
{
    /**
     * @param Viewable $viewable
     * @return $this
     */
    public function add(Viewable $viewable)
    {
        $keys = session()->get(config('recently-viewed.session_prefix') . '.' . get_class($viewable));
        if (!is_array($keys)) {
            $keys = [];
        }
        array_unshift($keys, $viewable->getKey());
        $keys = array_slice(array_unique($keys), 0, $viewable->getRecentlyViewsLimit());
        session()->put(
            config('recently-viewed.session_prefix') . '.' . get_class($viewable),
            $keys
        );

        if (config('recently-viewed.persist_enabled')) {
            $this->persist($viewable, $keys);
        }

        return $this;
    }

    /**
     * @param Viewable|string $viewable
     * @return mixed
     * @throws ShouldBeViewableException
     */
    public function getQuery($viewable)
    {
        if (!($viewable instanceof Viewable) && is_string($viewable)) {
            $viewable = new $viewable();
        }
        if (!($viewable instanceof Viewable)) {
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
        if (!($viewable instanceof Viewable) && is_string($viewable)) {
            $viewable = new $viewable();
        }
        if (!($viewable instanceof Viewable)) {
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
        if (!($viewable instanceof Viewable) && is_string($viewable)) {
            $viewable = new $viewable();
        }
        if (!($viewable instanceof Viewable)) {
            throw new ShouldBeViewableException('Entity should implement Viewable interface');
        }

        session()->forget(config('recently-viewed.session_prefix') . '.' . get_class($viewable));

        if (config('recently-viewed.persist_enabled')) {
            $this->clearPersist($viewable);
        }

        return $this;
    }

    /**
     * @return RecentlyViewed
     */
    public function clearAll(): RecentlyViewed
    {
        session()->forget(config('recently-viewed.session_prefix'));

        if (config('recently-viewed.persist_enabled')) {
            $this->clearPersistAll();
        }

        return $this;
    }

    /**
     * @param Viewable $viewable
     * @param array $data
     * @return RecentlyViewed
     */
    public function persist(Viewable $viewable, array $data): RecentlyViewed
    {
        if ($viewer = $this->getViewer()) {
            $viewer->syncRecentViews(get_class($viewable), $data);
        }

        return $this;
    }

    /**
     * @param Viewable $viewable
     * @return RecentlyViewed
     */
    public function clearPersist(Viewable $viewable): RecentlyViewed
    {
        if ($viewer = $this->getViewer()) {
            $viewer->deleteRecentViews([get_class($viewable)]);
        }

        return $this;
    }

    /**
     * @return RecentlyViewed
     */
    public function clearPersistAll(): RecentlyViewed
    {
        if ($viewer = $this->getViewer()) {
            $viewer->deleteRecentViews();
        }

        return $this;
    }

    /**
     * @return RecentlyViewed
     */
    public function mergePersistToCurrentSession(): RecentlyViewed
    {
        if ($viewer = $this->getViewer()) {
            $persist = $viewer->getRecentViews();
            $session = session()->get(config('recently-viewed.session_prefix'));
            $merged = [];
            foreach ($session as $type => $keys) {
                $obj = new $type();
                if ($obj instanceof Viewable) {
                    $limit = $obj->getRecentlyViewsLimit();
                    if (count($keys) >= $limit) {
                        $keys = array_slice($keys, 0, $limit);
                    } else {
                        if (isset($persist[$type])) {
                            $keys = array_slice(array_merge($keys, $persist[$type]), 0, $limit);
                        }
                    }
                    $keys = array_unique($keys);
                    if (count($keys)) {
                        $merged[$type] = array_unique($keys);
                    }
                }
                if (isset($persist[$type])) {
                    unset($persist[$type]);
                }
            }

            foreach ($persist as $type => $keys) {
                $obj = new $type();
                if ($obj instanceof Viewable) {
                    $limit = $obj->getRecentlyViewsLimit();
                    if (count($keys)) {
                        $merged[$type] = array_slice($keys, 0, $limit);
                    }
                }
            }
            session()->put(
                config('recently-viewed.session_prefix'),
                $merged
            );
            $viewer->deleteRecentViews();
            foreach ($merged as $type => $keys) {
                $viewer->syncRecentViews($type, $keys);
            }
        }

        return $this;
    }

    protected function getViewer(): ?Viewer
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user instanceof Viewer) {
                return $user;
            }
        }

        return null;
    }
}
