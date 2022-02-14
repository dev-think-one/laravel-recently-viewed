<?php


namespace RecentlyViewed\Models;

use Illuminate\Database\Eloquent\Model;

class RecentViews extends Model
{
    protected $guarded = [];

    public function getTable()
    {
        return config('recently-viewed.persist_table');
    }
}
