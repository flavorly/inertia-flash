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
        // Unpack the variables in case if its serialized.
        $this->resolveContainerValues();
        // then for each item that is still on the container if any, we will inertia share them :)
        $this->syncToInertia();
        // We need to Flush also
        session()->forget(config('inertia-flash.session-key','inertia-container'));
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

        if($value instanceof Closure) {
            $value = new SerializableClosure($value);
        }

        if($append) {
            $current = array_merge_recursive($this->container->get($key, []), [$value]);
            $this->container->put($key, $current);
        } else {
            $this->container->put($key, $value);
        }

        $this->syncWithSessionAndInertia();

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
     * @return $this
     */
    public function forget(...$keys): static
    {
        $this->container->forget(...$keys);
        inertia()->forget(...$keys);
        $this->syncWithSessionAndInertia();
        return $this;
    }

    /**
     * Flush the items from the session
     *
     * @return $this
     */
    public function flush(): static
    {
        $keys = $this->container->keys();
        $this->container = collect([]);
        inertia()->forget($keys->toArray());
        session()->forget(config('inertia-flash.session-key','inertia-container'));
        return $this;
    }

    /**
     * Attempts to resolve the value recursively.
     *
     * @param $value
     * @return mixed
     * @throws PhpVersionNotSupportedException
     */
    protected function resolveValue($value): mixed
    {
        if($value instanceof SerializableClosure) {
            return $value->getClosure();
        }

        if(is_array($value)) {
            foreach($value as $key => $item) {
                $value[$key] = $this->resolveValue($item);
            }
        }

        return $value;
    }

    /**
     * Transforms the values, and attempts to resolve pending Serialized Closures
     *
     * @return void
     */
    protected function resolveContainerValues(): void
    {
        $this->container->transform(fn($value) => $this->resolveValue($value));
    }

    /**
     * Syncs to Inertia Share
     *
     * @return void
     */
    protected function syncToInertia(): void
    {
        $persistentKeys = config('inertia-flash.persistent-keys', []);
        if(!empty($persistentKeys)) {
            collect($persistentKeys)->each(fn($value, $key) => inertia()->share($key, $value));
        }
        $this->container->each(fn($value, $key) => inertia()->share($key, $value));
    }

    /**
     * Syncs to Inertia Share & Also for the Session
     *
     * @return $this
     */
    protected function syncWithSessionAndInertia(): static
    {
        $this->syncToInertia();
        session()->put(config('inertia-flash.session-key','inertia-container'), $this->container->toArray());
        return $this;
    }
}
