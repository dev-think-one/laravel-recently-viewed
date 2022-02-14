<?php

return [
    'session_prefix' => env('RECENTLY_VIEWED_SESSION_PREFIX', 'recently_viewed'),

    'persist_enabled' => (bool) env('RECENTLY_VIEWED_PERSIST_ENABLED', false),

    'persist_table'   => 'recent_views',
];
