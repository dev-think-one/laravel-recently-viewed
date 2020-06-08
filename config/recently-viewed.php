<?php

return [

    /*
     * Session prefix.
     */
    'session_prefix' => env('RECENTLY_VIEWED_SESSION_PREFIX', 'recently_viewed'),

    /**
     * Persist functionality
     */
    'persist_enabled' => (bool) env('RECENTLY_VIEWED_PERSIST_ENABLED', false),
    'persist_table' => 'recent_views',
    'persist_model' => \RecentlyViewed\Models\RecentViews::class,
];
