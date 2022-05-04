<?php

namespace Igerslike\InertiaFlash;

use Closure;
use Igerslike\InertiaFlash\Drivers\AbstractDriver;
use Igerslike\InertiaFlash\Drivers\CacheDriver;
use Igerslike\InertiaFlash\Drivers\SessionDriver;
use Igerslike\InertiaFlash\Exceptions\DriverNotSupportedException;
use Igerslike\InertiaFlash\Exceptions\PrimaryKeyNotFoundException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Inertia\Inertia;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Laravel\SerializableClosure\SerializableClosure;

class InertiaFlash
{
    use Macroable;

    protected Collection $container;
    protected ?AbstractDriver $driver = null;

    /**
     */
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
     *
     * Shares the Value with Inertia & Also stores it in the driver.
     *
     * @param  string  $key
     * @param $value
     * @param  bool  $append
     * @return $this
     * @throws PhpVersionNotSupportedException
     */
    public function share(string $key, $value, bool $append = false): static
    {
        // Ensure we serialize the value for sharing
        $value = $this->serializeValue($value);
        if($append) {
            $value = array_merge_recursive($this->container->get($key, []), [$value]);
        }
        $this->container->put($key, $value);

        $this->shareToDriver();
        return $this;
    }

    /**
     * Alias to share function, but to append
     *
     * @param  string  $key
     * @param $value
     * @return $this
     * @throws PhpVersionNotSupportedException
     */
    public function append(string $key, $value): static
    {
        return $this->share($key, $value, true);
    }

    /**
     * Share if condition is met
     *
     * @param  bool  $condition
     * @param  string  $key
     * @param $value
     * @param  bool  $append
     * @return $this
     * @throws PhpVersionNotSupportedException
     */
    public function shareIf(bool $condition,string $key, $value, bool $append = false): static
    {
        if($condition) {
            return $this->share($key, $value, $append);
        }
        return $this;
    }

    /**
     * Share if condition is met
     *
     * @param  bool  $condition
     * @param  string  $key
     * @param $value
     * @param  bool  $append
     * @return $this
     * @throws PhpVersionNotSupportedException
     */
    public function shareUnless(bool $condition, string $key, $value, bool $append = false): static
    {
        return $this->shareIf(!$condition, $key, $value, $append);
    }

    /**
     * Forget the value from the container & driver
     *
     * @param ...$keys
     * @return static
     */
    public function forget(...$keys): static
    {
        $this->container->forget(...$keys);
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
    public function flush(): static
    {
        $keys = $this->container->keys();
        $this->container = collect([]);
        inertia()->forget($keys->toArray());
        $this->flushDriver();
        return $this;
    }

    /**
     * Flush the driver only
     *
     * @return $this
     */
    public function flushDriver(): static
    {
        $this->getDriver()->flush();
        return $this;
    }

    /**
     * Syncs to Inertia Share
     *
     * @param  bool  $flush
     * @return InertiaFlash
     */
    public function shareToInertia(bool $flush = true): static
    {
        if(!$this->shouldIgnore()) {
            return $this;
        }

        // Unserialize/Unpack any pending Serialized Closures
        $this->unserializeContainerValues();

        // Persist the keys for emptiness
        $persistentKeys = config('inertia-flash.persistent-keys', []);
        if(!empty($persistentKeys)) {
            collect($persistentKeys)->each(fn($value, $key) => Inertia::share($key, $value));
        }

        // Share with Inertia
        $this->container->each(fn($value, $key) => Inertia::share($key, $value));

        // Flush on sharing
        if($flush && config('inertia-flash.flush', true)) {
            $this->flushDriver();
            $this->container = collect([]);
        }
        return $this;
    }

    /**
     * Get the params being shared for the container
     *
     * @param  bool  $flush
     * @return array
     */
    public function getShared(bool $flush = true): array
    {
        if(!$this->shouldIgnore()) {
            return [];
        }

        $container = clone $this->container;
        // Flush on sharing
        if($flush && config('inertia-flash.flush', true)) {
            $this->flushDriver();
            $this->container = collect([]);
        }
        return $container->toArray();
    }

    /**
     * Syncs to Inertia Share & Also for the driver
     *
     * @return $this
     */
    protected function shareToDriver(): static
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
     * @param  Request|null  $request
     * @return bool
     */
    public function shouldIgnore(?Request $request = null): bool
    {
        $request = $request ?? request();
        $ignoreUrls = collect(config('inertia-flash.ignore_urls', ['broadcasting/auth']));
        foreach($ignoreUrls as $url) {
            if (str_contains($request->url(), $url)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Attempt to Serialize closures
     * @throws PhpVersionNotSupportedException
     */
    protected function serializeValue($value)
    {
        if($value instanceof Closure) {
            return new SerializableClosure($value);
        }

        if(is_array($value)) {
            foreach($value as $key => $item) {

                // Other edge cases can be added here
                if(!$item instanceof Closure) {
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
     * @param $value
     * @return mixed
     * @throws PhpVersionNotSupportedException
     */
    protected function unserializeValue($value): mixed
    {
        if($value instanceof SerializableClosure) {
            return $value->getClosure();
        }

        if(is_array($value)) {
            foreach($value as $key => $item) {
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
    protected function unserializeContainerValues(): static
    {
        $this->container->transform(fn($value) => $this->unserializeValue($value));
        return $this;
    }

    /**
     * Transforms the values, and attempts to resolve pending Serialized Closures
     *
     * @return static
     */
    protected function serializeContainerValues(): static
    {
        $this->container->transform(fn($value) => $this->serializeValue($value));
        return $this;
    }

    /**
     * Binds the inertia flash to share to a specific user.
     *
     * @param  Authenticatable  $authenticatable
     * @return $this
     * @throws PrimaryKeyNotFoundException
     */
    public function forUser(Authenticatable $authenticatable): self
    {
        if(!$this->driver instanceof CacheDriver){
            throw new PrimaryKeyNotFoundException('You can only use the forUser method with a cache driver');
        }

        $this->getDriver()->setPrimaryKey($authenticatable->getKey());
        return $this;
    }

    /**
     * Get the driver instance.
     *
     * @return AbstractDriver
     * @throws DriverNotSupportedException
     */
    protected function getDriver(): AbstractDriver
    {
        if(null !== $this->driver) {
            return $this->driver;
        }

        $driver = config('inertia-flash.driver', 'session');
        if(!in_array($driver, ['session','cache'])) {
            throw new DriverNotSupportedException($driver);
        }

        $this->driver = match($driver){
            'session' => app(config('inertia-flash.session_driver', SessionDriver::class)),
            'cache' => app(config('inertia-flash.cache-driver', CacheDriver::class)),
            default => 'session',
        };

        return $this->driver;
    }
}
