# Laravel: Recently Viewed
Add functionality to save/get in session recently viewed entities (**Note**: on the current version used jusr user session without any persist storage)

You can track any number of entities. Each list will be saved separately.

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
use RecentlyViewed\Models\Traits\CanBeViewed;

class Property implements Viewable
{
    use CanBeViewed;

     // ...

    public function getKey() {
       return $this->ID;
    }
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

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/)
