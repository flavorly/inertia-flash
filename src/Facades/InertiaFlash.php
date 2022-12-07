<?php

namespace flavorly\InertiaFlash\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \flavorly\InertiaFlash\InertiaFlash
 */
class InertiaFlash extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'inertia-flash';
    }
}
