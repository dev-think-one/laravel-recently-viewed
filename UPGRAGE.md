# Upgrade guide

## v2 ===> v3

1. Config key `persist_model` is removed, please use new method:

```php
\RecentlyViewed\PersistManager::useRecentlyViewedModel(MyRecentViews::class);
```

2. Since v3, no need publish migrations, you can just enable and migrate

```php
\RecentlyViewed\PersistManager::enableMigrations();
```
