<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Driver Configuration
    |--------------------------------------------------------------------------
    |
    | You can configure inertia flash to use session or cache as the driver.
    | when using the cache driver inertia flash will leverage your current
    | cache driver and attempt to save the temporary shared keys there.
    | A unique key is used to generate the unique key for each user
    |
    | Drivers: 'cache' or 'session' are supported.
    | Prefix Key : inertia_container_
    | Cache TTL : Time in seconds to store the keys in cache.
    */

    'prefix_key' => 'inertia_container_',
    'driver' => 'session',

    'session-driver' => \Igerslike\InertiaFlash\Drivers\SessionDriver::class,
    'cache-driver' => \Igerslike\InertiaFlash\Drivers\CacheDriver::class,

    'cache-ttl' => 60,

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
