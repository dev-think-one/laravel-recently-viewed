<?php

namespace RecentlyViewed\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Orchestra\Testbench\Factories\UserFactory;
use RecentlyViewed\Models\Contracts\Viewer;
use RecentlyViewed\Models\Traits\CanView;

class User extends \Illuminate\Foundation\Auth\User implements Viewer
{
    use CanView, HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return new UserFactory();
    }
}
