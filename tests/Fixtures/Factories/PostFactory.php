<?php

namespace RecentlyViewed\Tests\Fixtures\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use RecentlyViewed\Tests\Fixtures\Models\Post;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

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
