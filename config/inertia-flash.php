<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Session Configuration
    |--------------------------------------------------------------------------
    |
    | Key to be used on session, when we flash the items. This should be a
    | a reserved and unique key.
    |
    */

    'session-key' => '_inertia_container',
    'flush' => true,

    /*
    |--------------------------------------------------------------------------
    | Persistent Keys
    |--------------------------------------------------------------------------
    |
    | Here you may configure the keys that should be persisted on the session,
    | even if they are empty they will be mapped to their primitives configured here.
    |
    */

    'persistent-keys' => [
        // foo, bar, baz
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware to register the inertia share request
    | Default: 'web'
    |
    */
    'middleware' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Ignore URLs & Params
    |--------------------------------------------------------------------------
    |
    | The URls to ignore by default, because inertia runs on web middl
    | Default For URLS: ['broadcasting/auth']
    |
    */
    'ignore_urls' => [
        'broadcasting/auth',
    ],
];
