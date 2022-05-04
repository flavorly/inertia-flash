# Inertia Flash ⚡

<p align="center"><img src="./assets/cover.png"></p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/igerslike/inertia-flash.svg?style=flat-square)](https://packagist.org/packages/igerslike/inertia-flash)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/igerslike/inertia-flash/run-tests?label=tests)](https://github.com/igerslike/inertia-flash/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/igerslike/inertia-flash/Check%20&%20fix%20styling?label=code%20style)](https://github.com/igerslike/inertia-flash/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/igerslike/inertia-flash.svg?style=flat-square)](https://packagist.org/packages/igerslike/inertia-flash)

A Quick way to flash & share variables to [InertiaJS](https://inertiajs.com/) that persist on session or cache. Really useful for redirects & returns!
Sharing to Inertia anywhere :)

## Installation

You can install the package via composer:

```bash
composer require igerslike/inertia-flash
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="inertia-flash-config"
```

This is the contents of the published config file:

```php
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
    | The URls to ignore by default, because inertia runs on web middleware
    | Default For URLS: ['broadcasting/auth']
    |
    */
    'ignore_urls' => [
        'broadcasting/auth',
    ],
];
```

## Usage

You can use the Inertia Flash helper anywhere from your code and share your variables directly to InertiaJS.
Keep in the mind that the values will only be kept on the current or next request lifecycle, they will be flushed once shared to Inertia
You may also use closures that under-the-hood will be converted to Laravel Closure Serializer ( Previously Opis )

```php
use \Igerslike\InertiaFlash\InertiaFlash;

// Resolve from container
$flash = app(\Igerslike\InertiaFlash\InertiaFlash::class);
$flash->share('foo', 'bar');

// Or using the helper
inertia_flash()->share('foo', 'bar');

// With a closure that will be serialized
inertia_flash()->share('foo', fn() => 'bar');

// With a nested closure
inertia_flash()->share('foo', ['bar' => 'foo', 'baz' => fn() => 'bar']);

// On Controllers return back()
return back()->inertia('foo', 'bar');

// return back() + Closures
return back()->inertia('foo', function () {
    return 'bar';
});

// Or the way cool way
inertia_flash()->share('foo', fn() => 'bar');

// Returning + the cool way
return back()->inertia('foo', fn() => 'bar');


// Appending Data
inertia_flash()->('fruits', 'bananas',true);
inertia_flash()->share('fruits', 'oranges', true);

// Conditional Sharing
inertia_flash()->shareIf($foo === true, 'foo', 'bar');
inertia_flash()->shareUnless($foo === false, 'foo', 'bar');

// Appending
// You can also use append on regular share method as the third parameter
inertia_flash()->append('foo', 'bar');

// Sharing to a user
// Only available if driver is cache, otherwise session will always use the current logged user
inertia_flash()->forUser($user)->append('foo', 'bar');
```

# Why Inertia Flash?

This package is intended to be used with the [InertiaJS](https://inertiajs.com/) framework. 
Inertia provides a nice way to share variables, but sometimes you might want to share data from somewhere else in your code.

Few use cases :
- Sharing data before a redirect ( Ex: back()->with('foo','bar') can be replicated with back()->inertia('foo','bar') )
- Sharing data from a controller to a view without using Inertia::share()
- Sharing data from a service directly
- Sharing data from any point of your code before serving a request/page
- Sharing data from a command/job to a specific user
- Avoiding Inertia Middleware pollution with sharing session variables back and forth.
- etc..

If you are looking for real-time sharing this package might not be your best choice, and would recommend using [Laravel Echo](https://github.com/laravel/echo) paired together with Pusher or [Soketi](https://docs.soketi.app/).

## Testing

```bash
composer test
```

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [jon](https://github.com/igerslike)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
