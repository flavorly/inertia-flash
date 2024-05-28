<?php

namespace Flavorly\InertiaFlash\Drivers;

use Illuminate\Support\Collection;

abstract class AbstractDriver
{
    protected ?string $primaryKey = null;

    /**
     * Get the data on the driver
     *
     * @return Collection<(string|int),mixed>
     */
    abstract public function get(): Collection;

    /**
     * Put the data into the driver
     *
     * @param  Collection<(string|int),mixed>  $container
     */
    abstract public function put(Collection $container): void;

    /**
     * Flush the data available on the driver
     */
    abstract public function flush(): void;

    /**
     * Set the Primary Key
     *
     * @return $this
     */
    public function setPrimaryKey(string $key): static
    {
        $this->primaryKey = $key;

        return $this;
    }

    /**
     * Gets & Generates the primary key
     */
    protected function key(): string
    {
        return implode(
            '_',
            [
                config('inertia-flash.prefix_key', 'inertia_container_'),
                $this->primaryKey ?? 'default',
            ]
        );
    }
}
