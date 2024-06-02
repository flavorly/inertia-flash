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

    'session-driver' => \Flavorly\InertiaFlash\Drivers\SessionDriver::class,
    'cache-driver' => \Flavorly\InertiaFlash\Drivers\CacheDriver::class,

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

    /*
   |--------------------------------------------------------------------------
   | Notifications Configuration
   |--------------------------------------------------------------------------
   |
   | This contains the basic notifications configuration for the Flash Notifications
   | System. Where we can set the queues, etc
   |
   */
    'notifications' => [

        // The default notification to be used must implement the contract
        'base_notification' => Flavorly\InertiaFlash\Notification\Notifications\DispatchableFlashNotification::class,

        // Class responsible for reading the notification
        'readable' => Flavorly\InertiaFlash\Notification\Actions\NotificationReadAction::class,

        // Defaults for the notification
        'defaults' => [
            'timeout' => 5000,
            'namespace' => 'flashNotifications',
            'read_route' => 'notification.read',
            'type' => Flavorly\InertiaFlash\Notification\Enums\NotificationTypeEnum::Flash,
            'level' => Flavorly\InertiaFlash\Notification\Enums\NotificationLevelEnum::Info,
            'via' => [
                //Flavorly\InertiaFlash\Notification\Enums\NotificationViaEnum::Inertia,
                //Flavorly\InertiaFlash\Notification\Enums\NotificationViaEnum::Database,
            ],
        ],

        // Queues for the notifications channels
        'queues' => [
            'database' => 'default',
            'mail' => 'default',
            'broadcast' => 'default',
        ],

        // Connections for the notifications channels
        'connections' => [
            'broadcast' => 'redis',
            'database' => 'sync',
            'inertia' => 'sync',
        ],
    ],
];
