# Inertia Flash

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
];
```

## Usage

```php
$inertiaFlash = new Igerslike\InertiaFlash();
echo $inertiaFlash->echoPhrase('Hello, Igerslike!');
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
