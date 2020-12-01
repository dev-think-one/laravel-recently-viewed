<?php


namespace RecentlyViewed\Models;

use Illuminate\Database\Eloquent\Model;

class RecentViews extends Model
{
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->table = \config('recently-viewed.persist_table');

        parent::__construct($attributes);
    }
}
