# Laravel: Recently Viewed
Add functionality to save/get in session recently viewed entities

You can track any number of entities. Each list will be saved separately.

## Session storage (without persist)

For example:
```
"recently_viewed" => array:2 [
                      "App\Models\Product" => array:2 [
                        0 => 'a3cda131-e599-4802-84ea-a3dddc19fa8c'
                        1 => '4413b636-9752-43b3-8361-3ef38c27acf9'
                      ]
                      "App\Domain\Property" => array:3 [
                        0 => 133
                        1 => 134
                        2 => 653
                      ]
                    ]
```
 
## Installation

You can install the package via composer:

```bash
composer require yaroslawww/laravel-recently-viewed
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="RecentlyViewed\ServiceProvider" --tag="config"
```

Configuration in *.env*
```dotenv
# Optional
# RECENTLY_VIEWED_SESSION_PREFIX=recently_viewed
```

## Usage example

```php
<?php
use Illuminate\Database\Eloquent\Model;
use RecentlyViewed\Models\Contracts\Viewable;
use RecentlyViewed\Models\Traits\CanBeViewed;

class Product extends Model implements Viewable
{
    use CanBeViewed;
}

// or

use RecentlyViewed\Models\Contracts\Viewable;

class Property implements Viewable
{ 
   // implement interface
}
```


```php
<?php
class ProductController extends Controller
 {
     public function show(Product $product)
     {
         \RecentlyViewed\Facades\RecentlyViewed::add($product);
 
         return view('my-view');
     }
 }
```

```php
<?php
class ProductsViewComposer
{
    public function compose(View $view)
    {
        $view->with([
            'recentlyViewedProducts' => \RecentlyViewed\Facades\RecentlyViewed::get(Product::class),
            // or
            'recentlyViewedProductsFiltered' => \RecentlyViewed\Facades\RecentlyViewed::getQuery(Product::class)
            ->where('not_display_in_recently_list', false)->get(),
        ]);
    }
}
```

## Add persist storage

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="RecentlyViewed\ServiceProvider" --tag="migrations"
php artisan migrate
```

Configuration in *.env*
```dotenv
RECENTLY_VIEWED_PERSIST_ENABLED=true
```

```php
use RecentlyViewed\Models\Contracts\Viewer;
use RecentlyViewed\Models\Traits\CanView;

class User extends Authenticatable implements Viewer
{
    use CanView;

    // ...
}
```

Add "merge" method after login (if you want merge saved data before login and already stored data)
```php
class LoginController extends Controller
{
    // ...

    protected function authenticated(Request $request, $user)
    {
        \RecentlyViewed::mergePersistToCurrentSession();
    }
}
```

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/)
