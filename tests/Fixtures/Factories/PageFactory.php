<?php

namespace RecentlyViewed\Tests\Fixtures\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use RecentlyViewed\Tests\Fixtures\Models\Page;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Page::class;

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
