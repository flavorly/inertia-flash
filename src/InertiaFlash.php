<?php

namespace Flavorly\InertiaFlash;

use Closure;
use Flavorly\InertiaFlash\Drivers\AbstractDriver;
use Flavorly\InertiaFlash\Drivers\CacheDriver;
use Flavorly\InertiaFlash\Drivers\SessionDriver;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Inertia\Inertia;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Laravel\SerializableClosure\SerializableClosure;
use Throwable;

final class InertiaFlash
{
    use Macroable;

    /**
     * Contains all the shared values
     *
     * @var Collection<(int|string),mixed>
     */
    protected Collection $container;

    /**
     * Actual Driver
     */
    protected ?AbstractDriver $driver = null;

    public function __construct()
    {
        // Boot the driver
        $this->getDriver();
        // On build, we will pull from driver.
        $this->container = $this->getDriver()->get();
        // We need to Flush also
        $this->flushDriver();
    }

    /**
     * Shares the Value with Inertia & Also stores it in the driver.
     */
    public function share(string $key, mixed $value, bool $append = false): InertiaFlash
    {
        try {
            $value = $this->serializeValue($value);
        } catch (Throwable $e) {
        }

        if ($append) {
            /** @var array<(string|int),mixed> $current */
            $current = $this->container->get($key, []);
            $value = array_merge_recursive(
                $current,
                [$value]
            );
        }

        $this->container->put($key, $value);

        $this->shareToDriver();

        return $this;
    }

    /**
     * Alias to share function, but to append
     */
    public function append(string $key, mixed $value): InertiaFlash
    {
        return $this->share($key, $value, true);
    }

    /**
     * Share if condition is met
     */
    public function shareIf(bool $condition, string $key, mixed $value, bool $append = false): InertiaFlash
    {
        if ($condition) {
            return $this->share($key, $value, $append);
        }

        return $this;
    }

    /**
     * Share if condition is met
     */
    public function shareUnless(bool $condition, string $key, mixed $value, bool $append = false): InertiaFlash
    {
        return $this->shareIf(! $condition, $key, $value, $append);
    }

    /**
     * Forget the value from the container & driver
     *
     * @return static
     */
    public function forget(mixed ...$keys): InertiaFlash
    {
        $this->container->forget(...$keys);
        // @phpstan-ignore-next-line
        inertia()->forget(...$keys);
        $this->shareToDriver();

        return $this;
    }

    /**
     * Flush the items from the driver
     * And also from inertia
     *
     * @return static
     */
    public function flush(): InertiaFlash
    {
        $keys = $this->container->keys();
        $this->container = collect();
        // @phpstan-ignore-next-line
        inertia()->forget($keys->toArray());
        $this->flushDriver();

        return $this;
    }

    /**
     * Flush the driver only
     */
    public function flushDriver(): InertiaFlash
    {
        $this->getDriver()->flush();

        return $this;
    }

    /**
     * Syncs to Inertia Share
     */
    public function shareToInertia(bool $flush = true): InertiaFlash
    {
        if (! $this->shouldIgnore()) {
            return $this;
        }

        // Unserialize/Unpack any pending Serialized Closures
        $this->unserializeContainerValues();

        // Persist the keys for emptiness
        $persistentKeys = config('inertia-flash.persistent-keys', []);
        if (! empty($persistentKeys)) {
            collect($persistentKeys)->each(fn ($value, $key) => Inertia::share($key, $value));
        }

        // Share with Inertia
        // @phpstan-ignore-next-line
        $this->container->each(fn ($value, $key) => Inertia::share($key, $value));

        // Flush on sharing
        if ($flush && config('inertia-flash.flush', true)) {
            $this->flushDriver();
            $this->container = collect();
        }

        return $this;
    }

    /**
     * Get the params being shared for the container
     *
     * @return array<(string|int),mixed>
     */
    public function getShared(bool $flush = true): array
    {
        if (! $this->shouldIgnore()) {
            return [];
        }

        $container = clone $this->container;
        // Flush on sharing
        if ($flush && config('inertia-flash.flush', true)) {
            $this->flushDriver();
            $this->container = collect();
        }

        return $container->toArray();
    }

    /**
     * Syncs to Inertia Share & Also for the driver
     */
    protected function shareToDriver(): InertiaFlash
    {
        // Need to pack/serialize to driver, because driver does not support closures
        // But it does take Laravel Serializable Closure
        $this->serializeContainerValues();

        // Then we are ready to put it in the driver
        $this->getDriver()->put($this->container);

        return $this;
    }

    /**
     * If it should be shared
     */
    public function shouldIgnore(?Request $request = null): bool
    {
        $request = $request ?? request();
        /**
         * @var Collection<int,string> $ignoreUrls
         */
        $ignoreUrls = collect(config('inertia-flash.ignore_urls', ['broadcasting/auth']));
        foreach ($ignoreUrls as $url) {
            if (str_contains($request->url(), $url)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Attempt to Serialize closures
     *
     * @throws PhpVersionNotSupportedException
     */
    protected function serializeValue(mixed $value): mixed
    {
        if ($value instanceof Closure) {
            return new SerializableClosure($value);
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {

                // Other edge cases can be added here
                if (! $item instanceof Closure) {
                    continue;
                }

                $value[$key] = $this->serializeValue($item);

            }
        }

        return $value;
    }

    /**
     * Attempts to resolve the value recursively.
     *
     * @throws PhpVersionNotSupportedException
     */
    protected function unserializeValue(mixed $value): mixed
    {
        if ($value instanceof SerializableClosure) {
            return $value->getClosure();
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->unserializeValue($item);
            }
        }

        return $value;
    }

    /**
     * Transforms the values, and attempts to resolve pending Serialized Closures
     *
     * @return static
     */
    protected function unserializeContainerValues(): InertiaFlash
    {
        $this->container->transform(fn ($value) => $this->unserializeValue($value));

        return $this;
    }

    /**
     * Transforms the values, and attempts to resolve pending Serialized Closures
     *
     * @return static
     */
    protected function serializeContainerValues(): InertiaFlash
    {
        $this->container->transform(fn ($value) => $this->serializeValue($value));

        return $this;
    }

    /**
     * Binds the inertia flash to share to a specific user.
     */
    public function forUser(Authenticatable $authenticatable): self
    {
        if (! $this->driver instanceof CacheDriver) {
            return $this;
        }

        // @phpstan-ignore-next-line
        $this->getDriver()->setPrimaryKey($authenticatable->getKey());

        return $this;
    }

    /**
     * Get the driver instance.
     */
    protected function getDriver(): AbstractDriver
    {
        if ($this->driver !== null) {
            return $this->driver;
        }

        $driver = config('inertia-flash.driver', 'session');
        if (! in_array($driver, ['session', 'cache'])) {
            $driver = 'session';
        }

        $this->driver = match ($driver) {
            // @phpstan-ignore-next-line
            'session' => app(config('inertia-flash.session_driver', SessionDriver::class)),
            // @phpstan-ignore-next-line
            'cache' => app(config('inertia-flash.cache-driver', CacheDriver::class)),
            default => 'session',
        };

        return $this->driver;
    }
}
