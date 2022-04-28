<?php

namespace Igerslike\InertiaFlash;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Laravel\SerializableClosure\SerializableClosure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class InertiaFlash
{
    use Macroable;

    protected Collection $container;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct()
    {
        // On build, we will pull from session.
        $this->container = collect(session()->get(config('inertia-flash.session-key','inertia-container'), []));
        // We need to Flush also
        $this->flushSession();
    }

    /**
     *
     * Shares the Value with Inertia & Also stores it in the session.
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

        $this->shareToSession();
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
     * Forget the value from the container & session
     *
     * @param ...$keys
     * @return static
     */
    public function forget(...$keys): static
    {
        $this->container->forget(...$keys);
        inertia()->forget(...$keys);
        $this->shareToSession();
        return $this;
    }

    /**
     * Flush the items from the session
     * And also from inertia
     *
     * @return static
     */
    public function flush(): static
    {
        $keys = $this->container->keys();
        $this->container = collect([]);
        inertia()->forget($keys->toArray());
        $this->flushSession();
        return $this;
    }

    /**
     * Flush the session only
     *
     * @return $this
     */
    public function flushSession(): static
    {
        session()->forget(config('inertia-flash.session-key','inertia-container'));
        return $this;
    }

    /**
     * Syncs to Inertia Share
     *
     * @param  bool  $flushSession
     * @return InertiaFlash
     */
    public function shareToInertia(bool $flushSession = true): static
    {
        // Unserialize/Unpack any pending Serialized Closures
        $this->unserializeContainerValues();

        // Persist the keys for emptiness
        $persistentKeys = config('inertia-flash.persistent-keys', []);
        if(!empty($persistentKeys)) {
            collect($persistentKeys)->each(fn($value, $key) => inertia()->share($key, $value));
        }

        // Share with Inertia
        $this->container->each(fn($value, $key) => inertia()->share($key, $value));

        // Flush on sharing
        if($flushSession && config('inertia-flash.flush', true)) {
            $this->flushSession();
            $this->container = collect([]);
        }
        return $this;
    }

    /**
     * Syncs to Inertia Share & Also for the Session
     *
     * @return $this
     */
    protected function shareToSession(): static
    {
        // Need to pack/serialize to session, because session does not support closures
        // But it does take Laravel Serializable Closure
        $this->serializeContainerValues();

        // Then we are ready to put it in the session
        session()->put(config('inertia-flash.session-key','inertia-container'), $this->container->toArray());

        return $this;
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
}
