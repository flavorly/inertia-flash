# Inertia Flash ⚡

<p align="center"><img src="./assets/cover.png"></p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/flavorly/inertia-flash.svg?style=flat-square)](https://packagist.org/packages/flavorly/inertia-flash)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/flavorly/inertia-flash/run-tests?label=tests)](https://github.com/flavorly/inertia-flash/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/flavorly/inertia-flash/Check%20&%20fix%20styling?label=code%20style)](https://github.com/flavorly/inertia-flash/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/flavorly/inertia-flash.svg?style=flat-square)](https://packagist.org/packages/flavorly/inertia-flash)

A Quick way to flash & share variables to [InertiaJS](https://inertiajs.com/) that persist on session or cache. Really useful for redirects & returns!
Sharing to Inertia anywhere :)

## Installation

You can install the package via composer:

```bash
composer require flavorly/inertia-flash
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
        // 'some-key' => 'some-value',
        // 'messages => [],
    ],


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

## 1) Inertia Share

You can use the Inertia Flash helper anywhere from your code and share your variables directly to InertiaJS.
Keep in the mind that the values will only be kept on the current or next request lifecycle, they will be flushed once shared to Inertia
You may also use closures that under-the-hood will be converted to Laravel Closure Serializer ( Previously Opis )

```php
use Flavorly\InertiaFlash\InertiaFlash;

// Resolve from container
$flash = app(\Flavorly\InertiaFlash\InertiaFlash::class);
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
inertia_flash()->share('fruits', 'bananas',true);
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

## 2) Notifications

This package also provide a nice way to build a agnostic notification system that can be shared to Inertia or Other Frameworks

Here is a basic example of usage:

```php
  notification()
    ->message('Thanks for your order! Your welcome on Site! ')
    ->viaInertia()
    ->dispatch();
```

### 2.1) Notifications Channels

The Package provides 4 different ways to forward notifications, here is a quick breakdown:

- Via Inertia - This will share the notification to frontend, resulting in a property being injected on the shared data, by default it goes into "notifications", this can be changed, it will contain an array of notifications there.
- Via Database - This will use Laravel Notifications system to persist the notification on the database
- Via Broadcast - this will use Laravel Echo to broadcast the notification to the frontend
- Via Mail - this will use Laravel Mail to send the notification to the user

Keep in mind that you can always override all this channels & the notification yourself by extending the original notification class or providing one on the configuration, Please do check the `config.php` for more information

### 2.2) Notifications Content Blocks

Usually notifications contain a title, message & icon, but there is some cases where you want more, we provide a simple abstraction for simple content blocks
This is useful for Dialogs, where you want to show more information, keep in mind this is really simple, anything more complex should be taken care on the frontend

Here is a quick example

```php
  notification()
    ->dialog()
    ->title('Thanks for your order!')
    ->message('Thanks for your order! Your welcome on Site! ')
    ->icon('🎉')
    ->block(fn (NotificationContentBlock $block) => $block->icon('🎉'))
    ->block(fn (NotificationContentBlock $block) => $block->title('Thanks for your order!'))
    ->block(fn (NotificationContentBlock $block) => $block->text('Your welcome on Site!'))
    ->block(fn (NotificationContentBlock $block) => $block->image('https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExeXRmMHp4N2o1bjQ2ajg0bXEyMmt5OXJrdW8zcmxqbHJ1MTNjZmdxbyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/Cm9wKmKMUlRPvdoHgU/giphy-downsized-large.gif'))
    ->viaInertia()
    ->dispatch();
```

### 2.3) Notifications Icon

By default the notification doesnt contain any icon, we send a "level" back, so you can decide on the frontend how you will handle the icon based on the level
But you can also pass in more complex icons or raw icons ( say emojis ) for the notification icon.

```php
  notification()
    ->message('Thanks for your order! Your welcome on Site! ')
    ->icon('🎉')
    ->dispatch();
```

### 2.4) Notifications Levels & Types

By default a level `info` and type `flash` is set, you can change this by calling fluent methods.
Different types of notifications can usefull across the app, while Flash notifications are more useful to show quick messages, dialogs can be also useful to show more detailed information.


```php
  notification()
    ->message('Thanks for your order! Your welcome on Site!')
    ->success()
    ->dialog()
    ->dispatch();

  notification()
    ->message('Thanks for your order! Your welcome on Site!')
    ->error()
    ->toast()
    ->dispatch();

  notification()
    ->message('Thanks for your order! Your welcome on Site!')
    ->warning()
    ->flash()
    ->dispatch();
```


### 2.5) Notifications Advanced

There is a lot more to explore on the notifications advanced options, so im going to highlight some of them here:

- Calling `dispatch()` will usually "queue" the notification sending depending on your driver, call `dispatchNow()` to send it immediately
- Queues & Connections can be configured in the config file, same structure as Laravel.
- By default no channels are enabled, you can chain `viaInertia()`, `viaDatabase()`, `viaBroadcast()`, `viaMail()` to enable them, or use the configuration to set a default channel.
- By default, when using Broadcast or Database, we will try to resolve the notified to the current logged user, but you can always use `toUser()` or `to($notifiable)` to send it to a specific user/model.
- When using Database Notifications a URL for `readable` is generated, you can override this by calling `readable($url)` on the notification.
- You can also use `readable()` to generate a URL based on the current request.
- There is a current issue that when sharing with Inertia & Database Notifications, the notification will be shared to the frontend, the ID of the inertia notification is a auto-generated one since the record is only created later. Use a Listener to update or just use Broadcast

### 2.6) Notifications Frontend Implementation

Here is an example of a component using Shadcn for Flash notifications, supporting emojis, icons and iconify for icons.

```php
notification()
    ->title('Thanks for your order!'.time())
    ->message('Thanks for your order! Your welcome on site! '.time())
    ->icon('majesticons:add-column')
    ->dispatch();
```

```tsx
import { useContext } from 'react'
import axios from 'axios'
import { AnimatePresence, LayoutGroup, motion } from 'framer-motion'
import { each } from 'lodash-es'
import { AlertFlash } from '@ui/alert-flash'
import { NotificationsFlashContext } from './notifications-context'
import type { AlertProps } from '@ui/design/alert'

export function NotificationsFlash(){
    const { state, api } = useContext(NotificationsFlashContext)

    if(!state || !api) {
        throw new Error('NotificationsFlash must be used within a provider')
    }
    
    const { notifications } = usePage<{ notifications: Notification.FlashNotification[] }>().props
    useEffect(
        () => {
            each(notifications, (notification: Notification.FlashNotification) => {
                api.push(notification)
            })
        },
        [notifications]
    )

    const onClose = (item: Notification.FlashNotification) => {
        if(item.readable?.enable && item.readable.url) {
            axios({
                method: item.readable.method,
                url: item.readable.url,
            }).then(() => {
                api.pull(item)
            })
            return
        }
        api.pull(item)
    }

    const convertLevelToVariant = (level: Notification.Enums.NotificationLevelEnum): AlertProps['variant'] => {
        switch(level) {
            case 'success':
                return 'green'
            case 'info':
                return 'blue'
            case 'warning':
                return 'warning'
            case 'error':
                return 'destructive'
            default:
                return 'blue'
        }
    }

    if(!state.items.length) {
        return null
    }

    return (
        <LayoutGroup>
            <div className="grid grid-cols-1 gap-y-2">
                <AnimatePresence>
                    {state.items.map((item) => (
                        <motion.div
                            animate={{ opacity: 1, scale: 1 }}
                            exit={{ opacity: 0, scale: 0.9 }}
                            initial={{ opacity: 0, scale: 0.9 }}
                            key={item.id}
                            transition={{ duration: 0.3, easing: 'ease-in-out' }}
                        >
                            { item.shown ? (
                                <AlertFlash
                                    closable
                                    icon={item.icon?.content}
                                    iconProps={item.icon?.props as any}
                                    text={item.message}
                                    title={item.title}
                                    variant={convertLevelToVariant(item.level)}
                                    onClose={() => { onClose(item) }}
                                />
                            ) : null }
                        </motion.div>
                    ))}
                </AnimatePresence>
            </div>
        </LayoutGroup>
    )
}
````


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

- [jon](https://github.com/flavorly)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
