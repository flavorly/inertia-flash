# Inertia Flash ⚡

<p align="center"><img src="./assets/cover.png"></p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/igerslike/inertia-flash.svg?style=flat-square)](https://packagist.org/packages/igerslike/inertia-flash)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/igerslike/inertia-flash/run-tests?label=tests)](https://github.com/igerslike/inertia-flash/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/igerslike/inertia-flash/Check%20&%20fix%20styling?label=code%20style)](https://github.com/igerslike/inertia-flash/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/igerslike/inertia-flash.svg?style=flat-square)](https://packagist.org/packages/igerslike/inertia-flash)

A Quick way to flash & share variables to [InertiaJS](https://inertiajs.com/) that persist on session.

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
return [
  /*
    |--------------------------------------------------------------------------
    | Session Key
    |--------------------------------------------------------------------------
    |
    | Key to be used on session, when we flash the items. This should be a
    | a reserved and unique key.
    |
    */

    'session-key' => 'inertia-container',

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
];
```

## Usage

You can use the Inertia Flash helper anywhere from your code and share your variables directly to InertiaJS.
Keep in the mind that the values will only be kept on the next request lifecycle, they will be flushed once shared to Inertia
You may also use closures that under-the-hood will be converted to Laravel Closure Serializer ( Previously Opis )

```php
use \Igerslike\InertiaFlash\InertiaFlash;

// Resolve from container
$flash = app(\Igerslike\InertiaFlash\InertiaFlash::class);
$flash->share('foo', 'bar');

// Or using the helper
inertia_flash()->share('foo', 'bar');


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
inertia_flash()->share('fruits', 'bananas');
inertia_flash()->share('fruits', 'oranges');

```

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
