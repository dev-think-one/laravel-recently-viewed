<?php

namespace RecentlyViewed;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use RecentlyViewed\Exceptions\ShouldBeViewableException;
use RecentlyViewed\Models\Contracts\Viewable;
use RecentlyViewed\Models\Contracts\Viewer;

class RecentlyViewed
{
    protected string $sessionPrefix;

    public function __construct()
    {
        $this->sessionPrefix = config('recently-viewed.session_prefix');
    }

    public function add(Viewable $viewable): static
    {
        if (method_exists($viewable, 'getKey')) {
            $keys = session()->get("{$this->sessionPrefix}.".get_class($viewable));
            if (!is_array($keys)) {
                $keys = [];
            }
            array_unshift($keys, $viewable->getKey());
            $keys = array_slice(array_unique($keys), 0, $viewable->getRecentlyViewsLimit());
            session()->put(
                "{$this->sessionPrefix}.".get_class($viewable),
                $keys
            );

            if (PersistManager::isEnabled()) {
                $this->persist($viewable, $keys);
            }
        }

        return $this;
    }

    /**
     * @return Collection Eloquent collection of items.
     * @throws ShouldBeViewableException
     */
    public function get(Viewable|string $viewable, ?int $limit = null): Collection
    {
        throw_if(
            !is_a($viewable, Viewable::class, true),
            new ShouldBeViewableException('Entity should implement Viewable interface.')
        );

        if (is_string($viewable)) {
            $viewable = app()->make($viewable);
        }

        $keys = session()->get("{$this->sessionPrefix}.".get_class($viewable));

        if (!is_array($keys)) {
            $keys = [];
        }

        return $viewable
                   ->whereRecentlyViewedIn($keys)
                   ?->take($limit ?? $viewable->getRecentlyViewsLimit())
                   ->get()
                   ->sortBy(fn ($model) => array_search($model->getKey(), $keys)) ?? collect([]);
    }

    /**
     * @throws ShouldBeViewableException
     */
    public function clear(Viewable|string $viewable): static
    {
        throw_if(
            !is_a($viewable, Viewable::class, true),
            new ShouldBeViewableException('Entity should implement Viewable interface.')
        );

        if (is_string($viewable)) {
            $viewable = app()->make($viewable);
        }

        session()->forget("{$this->sessionPrefix}.".get_class($viewable));

        if (PersistManager::isEnabled()) {
            $this->clearPersist($viewable);
        }

        return $this;
    }

    public function clearAll(): static
    {
        session()->forget(config('recently-viewed.session_prefix'));

        if (PersistManager::isEnabled()) {
            $this->clearPersistAll();
        }

        return $this;
    }

    public function persist(Viewable $viewable, array $data): static
    {
        if ($viewer = $this->getViewer()) {
            $viewer->syncRecentViews(get_class($viewable), $data);
        }

        return $this;
    }

    public function clearPersist(Viewable $viewable): static
    {
        if ($viewer = $this->getViewer()) {
            $viewer->deleteRecentViews([get_class($viewable)]);
        }

        return $this;
    }

    public function clearPersistAll(): static
    {
        $this->getViewer()?->deleteRecentViews();

        return $this;
    }

    public function mergePersistToCurrentSession(): static
    {
        if ($viewer = $this->getViewer()) {
            $persist = $viewer->getRecentViews()->toArray();
            $session = session()->get($this->sessionPrefix);
            $merged  = [];
            if (is_array($session)) {
                foreach ($session as $type => $keys) {
                    if (!class_exists($type)) {
                        continue;
                    }
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
            }

            if (is_array($persist)) {
                foreach ($persist as $type => $keys) {
                    if (!class_exists($type)) {
                        continue;
                    }
                    $obj = new $type();
                    if ($obj instanceof Viewable) {
                        $limit = $obj->getRecentlyViewsLimit();
                        if (count($keys)) {
                            $merged[$type] = array_slice($keys, 0, $limit);
                        }
                    }
                }
            }
            session()->put($this->sessionPrefix, $merged);
            $viewer->deleteRecentViews();
            foreach ($merged as $type => $keys) {
                $viewer->syncRecentViews($type, $keys);
            }
        }

        return $this;
    }

    protected function getViewer(): ?Viewer
    {
        if (($user = Auth::user())
            && $user instanceof Viewer) {
            return $user;
        }

        return null;
    }
}
