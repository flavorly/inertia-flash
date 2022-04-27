<?php

namespace Igerslike\InertiaFlash\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Igerslike\InertiaFlash\InertiaFlash
 */
class InertiaFlash extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'inertia-flash';
    }
}
