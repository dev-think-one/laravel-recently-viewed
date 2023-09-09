<?php


namespace RecentlyViewed\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class UUIDRecentViews extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return config('recently-viewed.persist_table').'_uuid';
    }
}
