<?php

namespace flavorly\InertiaFlash\Drivers;

use flavorly\InertiaFlash\Exceptions\PrimaryKeyNotFoundException;
use Illuminate\Support\Collection;

abstract class AbstractDriver
{
    protected ?string $primaryKey = null;

    /**
     * Get the data on the driver
     * @return Collection
     */
    abstract function get(): Collection;

    /**
     * Put the data into the driver
     * @param  Collection  $container
     * @return void
     */
    abstract function put(Collection $container): void;

    /**
     * Flush the data available on the driver
     * @return void
     */
    abstract function flush(): void;

    /**
     * Set the Primary Key
     * @param  string  $key
     * @return $this
     */
    public function setPrimaryKey(string $key): static
    {
        $this->primaryKey = $key;
        return $this;
    }

    /**
     * Gets & Generates the primary key
     * @return string
     * @throws PrimaryKeyNotFoundException
     */
    protected function key(): string
    {
        if(null === $this->primaryKey){
            throw new PrimaryKeyNotFoundException();
        }

        return implode(
            '_',
            [
                config('inertia-flash.prefix_key','inertia_container_'),
                $this->primaryKey
            ]
        );
    }
}
