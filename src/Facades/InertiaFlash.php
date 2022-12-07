<?php

namespace Flavorly\InertiaFlash\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Flavorly\InertiaFlash\InertiaFlash
 */
class InertiaFlash extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'inertia-flash';
    }
}
