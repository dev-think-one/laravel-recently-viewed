<?php

namespace RecentlyViewed\Tests\Fixtures\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use RecentlyViewed\Tests\Fixtures\Models\Country;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Country::class;

    /**
     * @inheritDoc
     */
    public function definition(): array
    {
        return [
            'id'    => Str::uuid(),
            'title' => $this->faker->word(),
        ];
    }
}
