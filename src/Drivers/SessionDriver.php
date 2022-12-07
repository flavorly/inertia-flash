<?php

namespace Flavorly\InertiaFlash\Drivers;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

class SessionDriver extends AbstractDriver
{
    use Macroable;

    protected ?string $primaryKey = 'session';

    /**
     * @inheritDoc
     */
    public function get(): Collection
    {
        return collect(session()->get($this->key(), []));
    }

    /**
     * @inheritDoc
     */
    public function put(Collection $container): void
    {
        session()->put($this->key(), $container->toArray());
    }

    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        session()->forget($this->key());
    }
}
