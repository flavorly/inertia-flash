<?php

namespace Flavorly\InertiaFlash\Drivers;

use Carbon\Carbon;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

class CacheDriver extends AbstractDriver
{
    use Macroable;

    /**
     * The tags to be saved on the Tagged cached
     *
     * @var string[]
     */
    protected array $tags = [
        'inertia_container',
    ];

    public function __construct()
    {
        $this->discoverPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function get(): Collection
    {
        return collect($this->cache()->get($this->key()) ?? []);
    }

    /**
     * {@inheritdoc}
     */
    public function put(Collection $container): void
    {
        $this->cache()->remember(
            $this->key(),
            $this->cacheTime(),
            fn () => $container->toArray()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function flush(): void
    {
        $this->cache()->forget($this->key());
    }

    /**
     * Attempts to discover the primary key of the container.
     * Leverages the auth system, when used on frontend
     * takes the first user id as primary key.
     */
    protected function discoverPrimaryKey(): void
    {
        if ($this->primaryKey !== null) {
            return;
        }

        $key = auth()->id();
        if ($key === null) {
            $key = session()->getId();
        }

        $this->setPrimaryKey((string) $key);
    }

    /**
     * Get the cache manager instance.
     * If tags are support it will then tag before returning.
     */
    protected function cache(): CacheManager
    {
        /** @var CacheManager $cache */
        $cache = cache();
        if ($cache->supportsTags()) {
            $cache->tags($this->tags);
        }

        return $cache;
    }

    /**
     * Use carbon to get the cache ttl.
     */
    protected function cacheTime(): Carbon
    {
        // @phpstan-ignore-next-line
        return Carbon::now()->addSeconds(config('inertia-flash.cache-ttl', 60));
    }
}
