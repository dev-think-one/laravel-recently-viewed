<?php

namespace RecentlyViewed\Tests\Fixtures\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use RecentlyViewed\Tests\Fixtures\Models\Product;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * @inheritDoc
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
        ];
    }
}
