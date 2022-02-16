<?php

namespace RecentlyViewed\Tests\Fixtures\Factories;

use RecentlyViewed\Tests\Fixtures\Models\User;

class UserFactory extends \Orchestra\Testbench\Factories\UserFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;
}
